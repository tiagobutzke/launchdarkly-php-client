# config valid only for current version of Capistrano
lock '3.4.0'

#variables:
server 'foodoraqa.de', roles: [:volofront], primary: true
set :branch, ENV['branch'] || "master"
# Default value for :log_level is :debug; will probably set to error only eventually but for now
set :log_level, ENV['loglevel'] || "debug"
set :flush_opcache, ENV['flush_opcache'] || "no"
set :country_code, ENV['country_code'] || "de"
set :platform, ENV['platform'] || "volo"
set :cleanup, ENV['cleanup'] || "no"

# Deployment server info
set :application,   "volofront"
set :app_path,      "app"
set :web_path, 	    "web"
set :symfony_env,   "prod"
set :deploy_to,     "/var/www/#{fetch :stage}"
set :tmp_dir,       "#{fetch :deploy_to}/shared/tmp"
set :revision_file, "app/config/revision.yml"
set :filter, roles: [:"volofront" ]

# composer
set :composer_install_flags, '--no-dev --prefer-dist --no-interaction --optimize-autoloader'

# SCM info
set :scm, :git
set :application, 'volo-frontend'
set :repo_url, 'git@github.com:foodpanda/volo-frontend.git'

# General config stuff
set :keep_releases, 1
set :linked_dirs, fetch(:linked_dirs, []).push("#{fetch :app_path}/logs", "node_modules", "#{fetch :web_path}/bower_components", "#{fetch :app_path}/config/countries_parameters")
set :permission_method, :acl
set :slack_web_hook, 'https://hooks.slack.com/services/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'

# Confirmations will not be requested from the command line.
set :interactive_mode, false
set :copy_exclude, [".git/*", ".svn/*", ".DS_Store", ".gitignore"]

set :default_env, {
  'COMPOSER_PROCESS_TIMEOUT' => 600
}

# User details for the production server
set :use_sudo, false
set :ssh_options, {
  forward_agent: true,
  user: 'ubuntu'
}

local_user = fetch(:local_user)

SSHKit.config.command_map[:php] = "/usr/bin/env php"
SSHKit.config.command_map[:php_de] = "COUNTRY_CODE=de /usr/bin/env php"
SSHKit.config.command_map[:grunt] = "grunt"

def send_slack_message(message)
  run_locally do
    execute "curl -s -X POST --data-urlencode 'payload={\"channel\": \"#deploy\", \"username\": \"Mario\", \"text\": \":star: #{message}\", \"icon_emoji\": \":ghost:\"}' #{fetch(:slack_web_hook)}"
  end
end

namespace :foodora do
  set :app_dir, "/var/www/#{fetch :stage}"

  task :start do
    run_locally do
      tag_name = fetch(:branch)
      set :tag_name, tag_name
    end

    time_start = Time.now
    set :time_start, time_start
    puts "deploy starting at #{time_start} by #{local_user} for #{fetch :stage} at #{fetch :tag_name} (#{fetch :branch})"
  end

  task :finish do
    time_finish = Time.now
    total_deploy_time = time_finish.to_i - fetch(:time_start).to_i

    set :total_deploy_time, total_deploy_time
    invoke 'foodora:notify:send_slack_message_success'
    puts ""
  end

  task :setup_slot do
    on primary(:volofront) do
      within shared_path do
        if test("[ ! -d #{shared_path}/app/config/countries_parameters/dist ]")
          execute "cp -r /var/www/countries_parameters/* #{shared_path}/app/config/countries_parameters"
        end
      end
    end
  end

  task :assets_install do
    on roles(:all) do
      within release_path do
        execute :php_de, "#{release_path}/app/console assets:install"
        execute :php_de, "#{release_path}/app/console fos:js-routing:dump"
        execute :php_de, "#{release_path}/app/console volo:thumbor:dump"
        execute :npm, "install"
        execute :grunt, "deploy --env=prod"
      end
    end
  end

  task :updated_version do
    on roles(:all) do

      within release_path do
        execute "sed", "s/asset_version_placeholder/#{fetch(:current_revision)}/g", "-i" ,"#{release_path}/app/config/config.yml"

        execute "echo 'parameters:' > #{release_path}/#{fetch(:revision_file)}" 
        execute "echo '  health_check_branch_name: #{fetch(:branch)}' >> #{release_path}/#{fetch(:revision_file)}" 
        execute "echo '  health_check_branch_commit: #{fetch(:current_revision)}' >> #{release_path}/#{fetch(:revision_file)}" 
      end
    end
  end

  task :translation_sync do
    desc 'Sync translations'
    on roles(:all) do
      within release_path do
        execute :php_de, "#{release_path}/app/console foodora:translations:sync"
        execute "rm", "-rf", "#{fetch :cache_path}/#{fetch :symfony_env}"
      end
    end
  end

  #APC clear
  task :apc_clear do
    on roles(:"volofront") do |host|
      if fetch(:flush_opcache) == "yes"
        execute :"curl --silent http://localhost:30000/flush_apc.php || true"
      else
        execute :"curl --silent http://localhost:30000/flush_apc.php?flush=apc || true"
      end
    end
    puts "APC cleared"
  end

  task :deploy_cleanup do
    desc 'various post deploy cleanup tasks'
    #clean up release dir
    invoke 'deploy:cleanup'
    puts "post deploy clean up completed"
  end

  namespace :notify do
    desc 'notification tasks'
    desc 'notify skype successful deploy'
    task :send_slack_message_success do
      slack_message = "Deployment of foodora by #{local_user} to *#{fetch :stage}* (branch: *#{fetch :tag_name}*) successful. Total Time #{fetch :total_deploy_time} seconds :sweat_smile:"
      send_slack_message slack_message
    end

    desc 'notify slack failed deploy'
    task :send_slack_message_failed do
     message = "Deployment of foodora by #{local_user} to *#{fetch :stage}* (branch: *#{fetch :tag_name}*) failed. :sob:"
     send_slack_message message
    end

    desc 'notify newrelic api and front'
    task :send_newrelic_message do
      #run_locally do
      #  if "#{fetch :stage}" == 'staging'
      #    country_domain_volofront = capture("grep volofront_#{fetch :country_id}_domain /etc/puppet/environments/production/data/region/euvolo.yaml | sed 's/ //g' | cut -d ':' -f2 | sed 's/www/www-st/'")
      #  else
      #    country_domain_volofront = capture("grep volofront_#{fetch :country_id}_domain /etc/puppet/environments/production/data/region/euvolo.yaml | sed 's/ //g' | cut -d ':' -f2")
      #  end
      #end

      #  execute :"curl --silent --show-error --output /dev/null \
      #    -H \"x-api-key:\" \
      #    -d \"deployment[app_name]=#{country_domain_volofront}\" \
      #    -d \"deployment[revision]=#{fetch :tag_name} (#{fetch :current_revision})\" \
      #    -d \"deployment[description]=#{fetch :branch}\" \
      #    -d \"deployment[user]=#{local_user}\" \
      #    \"https://rpm.newrelic.com/deployments.xml\""
    end
  end
end

before :deploy, 'foodora:start'

namespace :deploy do
  before 'symfony:cache:warmup', 'foodora:translation_sync'
  before 'composer:install', 'foodora:updated_version'

  after 'deploy:updated', 'foodora:assets_install'

  after 'deploy:check:make_linked_dirs', 'foodora:setup_slot'
  after :published, 'foodora:apc_clear'        #APC clear

  if fetch(:cleanup) == 'yes'
    after :finishing, 'foodora:deploy_cleanup'   #clean up old releases
  end

  #after :finishing, 'foodora:notify:send_newrelic_message'

  task :failed do
   invoke 'foodora:notify:send_slack_message_failed'
  end
end

#the last message on the console
after :deploy, 'foodora:finish'
