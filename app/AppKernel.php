<?php

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Yaml\Yaml;

class AppKernel extends Kernel
{
    /**
     * Valid environments available
     * @var array
     */
    private static $validEnvironments = array('production', 'staging', 'test', 'development');

    /**
     * @var string
     */
    private $countryCode;

    const ENVIRONMENT_COUNTRY_CODE = 'COUNTRY_CODE';
    const PARAMETER_COUNTRY_CODE   = 'country_code';
    const CONFIG_COUNTRY_TEMPLATE  = 'config/countries_parameters/%s.yml';

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Foodpanda\Bundle\WebTranslateItBundle\FoodpandaWebTranslateItBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Volo\FrontendBundle\VoloFrontendBundle(),
            new Helthe\Bundle\TurbolinksBundle\HeltheTurbolinksBundle(),
            new Misd\PhoneNumberBundle\MisdPhoneNumberBundle(),
            new Jb\Bundle\PhumborBundle\JbPhumborBundle(),
            new Foodpanda\Bundle\Core\HealthBundle\FoodpandaCoreHealthBundle(),
            new Foodpanda\Bundle\Api\HealthBundle\FoodpandaApiHealthBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * @inheritdoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $parameters = Yaml::parse($this->getCountryParametersPathname());
        $environment = $this->getEnvironment();

        if ('test' !== $environment) {
            // environment for config files is not taken from AppKernel as it is defined in the parameters.yml
            if (empty($parameters['parameters']['environment'])
                || !in_array($parameters['parameters']['environment'], self::$validEnvironments)
            ) {
                throw new \InvalidArgumentException('Your env is not valid. Please set a valid value on parameters.yml ' . $this->getCountryParametersPathname());
            }

            // @todo?: fix inconsistency between $environment and $this->environment
            $environment = $parameters['parameters']['environment'];
        } else {
            $parameters = [];
        }

        $configFilePath = sprintf(__DIR__ . '/config/config_%s.yml', $environment);

        $loader->load($this->getCountryParametersPathname());
        $loader->load($configFilePath);
        $this->loadCountryConfig($loader, $parameters);
        $this->setDefaultTimezone($parameters);
    }

    /**
     * @param array $params
     */
    protected function setDefaultTimezone(array $params)
    {
        $countryCode = isset($params['parameters']['country_code']) ? $params['parameters']['country_code'] : null;

        if ($this->getCountryCodeEnvironmentVariable() === $countryCode && isset($params['parameters']['timezone'])) {
            date_default_timezone_set($params['parameters']['timezone']);
        }
    }

    /**
     * @param LoaderInterface $loader
     * @param array $parameters
     */
    protected function loadCountryConfig(LoaderInterface $loader, array $parameters)
    {
        if (!empty($parameters['parameters']['country_code'])) {
            $countryConfigFilePath = __DIR__ . '/config/country/' . $parameters['parameters']['country_code'] . '.yml';

            if (file_exists($countryConfigFilePath)) {
                $loader->load($countryConfigFilePath);
            }

            $countryEnvironmentConfigFilePath = sprintf(
                __DIR__ . '/config/country/%s_%s.yml',
                $parameters['parameters']['country_code'],
                $parameters['parameters']['environment']
            );

            if (file_exists($countryEnvironmentConfigFilePath)) {
                $loader->load($countryEnvironmentConfigFilePath);
            }
        }
    }

    /**
     * @return string
     */
    public static function getCountryCodeEnvironmentVariable()
    {
        $environmentCountryCode = !empty($_ENV[static::ENVIRONMENT_COUNTRY_CODE])
            ? $_ENV[static::ENVIRONMENT_COUNTRY_CODE]
            : null;

        if (empty($environmentCountryCode) && !empty($_SERVER[static::ENVIRONMENT_COUNTRY_CODE])) {
            $environmentCountryCode = $_SERVER[static::ENVIRONMENT_COUNTRY_CODE];
        }

        return $environmentCountryCode;
    }

    /**
     * @throws LogicException if cannot get the list
     * @return string[]
     */
    public static function getAvailableCountryCodes()
    {
        $countriesCodes = [];

        $finder = new Finder();
        $files = $finder
            ->files()
            ->name('*.yml')
            ->notName('test.yml')
            ->notName('common.yml')
            ->in(__DIR__ . '/config/countries_parameters');

        foreach ($files as $filename) {
            $countriesCodes[] = pathinfo($filename, PATHINFO_FILENAME);
        }

        if (count($countriesCodes) === 0) {
            throw new LogicException('No countries codes parameters files have been found: ' . static::CONFIG_COUNTRY_TEMPLATE);
        }
        return $countriesCodes;
    }

    /**
     * @return string
     */
    public function getCountryParametersPathname()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . sprintf(static::CONFIG_COUNTRY_TEMPLATE, $this->lookupCountryCode());
    }

    /**
     * @throws LogicException if no country code parameter has been set
     * @return string
     */
    public function lookupCountryCode()
    {
        $countryCode = $this->countryCode ?: static::getCountryCodeEnvironmentVariable();
        if (empty($countryCode)) {
            throw new LogicException(
                'Country Code not set and no "' . static::ENVIRONMENT_COUNTRY_CODE . '" environment variable set'
            );
        }

        return $countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return parent::getCacheDir() . DIRECTORY_SEPARATOR . $this->lookupCountryCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return parent::getLogDir() . DIRECTORY_SEPARATOR . $this->lookupCountryCode();
    }

    /**
     * {@inheritdoc}
     *
     * @throws LogicException if no country code parameter has been set
     */
    protected function getKernelParameters()
    {
        $kernelParameters = parent::getKernelParameters();
        $kernelParameters[static::PARAMETER_COUNTRY_CODE] = $this->lookupCountryCode();

        return $kernelParameters;
    }
}
