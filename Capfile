set :deploy_config_path, 'app/config/deploy.rb'
set :stage_config_path, 'app/config/deploy'

# Load DSL and set up stages
require 'capistrano/setup'

# Include default deployment tasks
require 'capistrano/deploy'

# custom tasks from `lib/capistrano/tasks' if you have any defined
#Dir.glob('lib/capistrano/tasks/*.rake').each { |r| import r } #disabling as it double loags the github.rake
#instead of importing all, importing manually and individually

require 'yaml'

require 'capistrano/symfony'

#needed for github.rake scm functions
#$:.unshift File.dirname("#{__FILE__}") + "/lib"
