<?php namespace Lti\Sitemap\Plugin;

/**
 * Loads all the default plugin values
 *
 * An object of this type is inserted in the options database table
 * whenever plugin settings are saved.
 *
 * Class Defaults
 * @package Lti\Sitemap\Plugin
 */
class Defaults {
	public $values;

	public function __construct() {
		$this->values = array(

		);
	}
}

/**
 * Defines default values for each field
 *
 * Class def
 * @package Lti\Sitemap\Plugin
 */
class def {

	/**
	 * @var string Name of the setting, which will be used throughout the app
	 */
	public $name;
	/**
	 * @var string Type of value (text, radio...)
	 */
	public $type;
	/**
	 * @var mixed Value when initialized
	 */
	public $default_value;
	/**
	 * @var bool Whether the setting has knock on effects on postbox values.
	 */
	public $impacts_user_settings;

	/**
	 * @param $name
	 * @param $type
	 * @param null $default_value
	 * @param bool $impacts_user_settings
	 */
	public function __construct( $name, $type, $default_value = null, $impacts_user_settings = false ) {
		$this->name                  = $name;
		$this->type                  = __NAMESPACE__ . "\\Field_" . $type;
		$this->default_value         = $default_value;
		$this->impacts_user_settings = $impacts_user_settings;
	}
}

/**
 * Puts all settings together
 *
 * Class Plugin_Settings
 * @package Lti\Sitemap\Plugin
 */
class Plugin_Settings {
	/**
	 * @param \stdClass $settings
	 */
	public function __construct( \stdClass $settings = null ) {

		$defaults = new Defaults();

		/**
		 * @var def $value
		 */
		foreach ( $defaults->values as $value ) {
			$storedValue = null;
			if ( isset( $settings->{$value->name} ) ) {
				$storedValue = $settings->{$value->name};
			}
			$className = $value->type;


			 //@TODO: why do we do this test?
			if ( ! is_null( $value->default_value ) ) {
				$this->{$value->name} = new $className( $storedValue, $value->default_value,
					$value->impacts_user_settings );
			} else {
				$this->{$value->name} = new $className( $storedValue, null, $value->impacts_user_settings );
			}
		}
	}

	public static function get_defaults() {
		return new self();
	}

	public function save( Array $values = array() ) {
		return new Plugin_Settings( (object) $values );
	}

	public function get( $value ) {
		if ( isset( $this->{$value} ) && ! empty( $this->{$value}->value ) && ! is_null( $this->{$value}->value ) ) {
			return $this->{$value}->value;
		}

		return null;
	}

	public function set( $key, $value, $type = "Text" ) {
		$className    = __NAMESPACE__ . "\\Field_" . $type;
		$this->{$key} = new $className( $value );
	}

	/**
	 * Comparing two Plugin_Settings objects
	 *
	 * @param Plugin_Settings $values
	 *
	 * @return array $changed key-value array of the properties that changed
	 */
	public function compare( $values ) {
		$changed       = array();
		$currentValues = get_object_vars( $this );
		$oldValues     = get_object_vars( $values );

		foreach ( $currentValues as $key => $value ) {
			if ( $value->isTracked ) {
				if ( isset( $oldValues[ $key ] ) && $oldValues[ $key ]->value != $value->value ) {
					$changed[ $key ] = $value->value;
				}
			}
		}

		return $changed;
	}
}

