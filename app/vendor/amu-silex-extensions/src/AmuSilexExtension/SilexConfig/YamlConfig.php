<?php

namespace AmuSilexExtension\SilexConfig;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class YamlConfig implements ServiceProviderInterface
{
    private $filenames;

    private $replacements = array();

    public function __construct($filenames, array $replacements = array())
    {
		$this->filenames = is_array( $filenames ) ? $filenames : array($filenames);
       
        if ($replacements) {
            foreach ($replacements as $key => $value) {
                $this->replacements['%'.$key.'%'] = $value;
            }
        }
    }

    public function register(Application $app)
    {
		$config = array();
		foreach ( $this->filenames as $filename )
		{
	        if (!file_exists($filename)) {
	            throw new \InvalidArgumentException(sprintf("The config file '%s' does not exist.", $filename));
	        }
			
			$parsed = Yaml::parse($filename);
			
	        if (null === $parsed) {
	            throw new \InvalidArgumentException(sprintf("The config file '%s' appears to be invalid YAML.", $filename));
	        }

	        $config = array_merge_recursive($config, $parsed);
		}
		
		$replacedConfig = array();

        foreach ($config as $name => $value) {
            $replacedConfig[$name] = $this->doReplacements($value);
        }
		$app['config'] = $replacedConfig;
    }

    private function doReplacements($value)
    {
        if (!$this->replacements) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doReplacements($v);
            }

            return $value;
        }

        return strtr($value, $this->replacements);
    }
}
