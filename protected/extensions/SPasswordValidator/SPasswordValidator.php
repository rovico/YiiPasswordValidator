<?php
/**
 * SPasswordValidator 
 * 
 * Validator for passwords.
 * Ensure password is strong (at least with default parameters)
 * 
 * @author Sébastien Monterisi <sebastienmonterisi@yahoo.fr>
 * @version 0.2
 */
class SPasswordValidator extends CValidator
{    
    /**
     *
     * @var int minimal number of characters
     */
    public $min = 6;

    /**
     * @var int minimal number of lower case characters
     */
    public $low = 1;

    /**
     * @var int minimal number of upper case characters
     */
    public $up = 1;

    /**
     *
     * @var int minimal number of special characters 
     */
    public $spec = 0;
    
    /**
     *
     * @var int  minimal number of digit characters 
     */
    public $digit = 1;

    /**
     * 
     * @var str message for error. Can contain {attribute},{tested_param},{found},{required} params
     */
    public $message = "{attribute} doesn't containt enough {tested_param} characters. {found} found whereas it must be at least {required}.";
    
    /**
     * 
     * @var i18nclass
     */
    public $i18ncategory = 'spasswordvalidator';
	
	/**
     * Permitted Upper Case characters
     * @var str character range
     */
	public $ucChars = 'A-Z';
	
	/**
     * Permitted Lowercase characters
     * @var str character range
     */
	public $lcChars = 'a-z';
	

    /**
     * Validation
     * 
     * Function checks whether fulfill this requirements  :
     * <ul>
     *  <li>is a string</li>
     *  <li>has the minimal number of lower case characters</li>
     *  <li>has the minimal number of upper case characters</li>
     *  <li>has the minimal number of digit characters </li>
     *  <li>has the minimal number of special characters </li>
     *  <li>has the minimal length is respected</li>
     * </ul>
     * @param CModel $object
     * @param string $attribute
     */
    protected function validateAttribute($object, $attribute)
    {
//        $this->checkParams();
        
        $value = $object->$attribute;

        // is a string
        if (!is_string($value))
        {
            $this->addError($object, 
                            $attribute, 
                            Yii::t( $this->i18ncategory, "{attribute} is a {type} and is must be a string." ), 
                            array('{attribute}' => $attribute, '{type}' => gettype($value))
            );
            return; // other checks will throw errors or exception, so end validation here.
        }

        // minimum length
        $this->min = (int) $this->min;
        $found = strlen($value);
        if ($found < $this->min)
        {
            $this->addErrorInternal($object, 
                                    $attribute, 
                                    "", 
                                    array('found' => $found, 'required' => $this->min)
            );
	    return;
        }
	
        // number of lower case characters
		$expression = '![' . $this->lcChars . ']!';
        $found = preg_match_all($expression, $value, $whatever);
        if ($found < $this->low)
        {
            $this->addErrorInternal($object, 
                            $attribute, 
                           'lower case',
                            array('found' => $found, 'required' => $this->low)
            );            
        }

        // number of upper case characters
		$expression = '![' . $this->ucChars . ']!';
        $found = preg_match_all($expression, $value, $whatever);
        if ($found < $this->up)
        {
            $this->addErrorInternal($object, 
                            $attribute, 
                            'upper case',
                            array('found' => $found, 'required' => $this->up)
            );
        }
        
        // special characters
        $found = preg_match_all('![\W]!', $value, $whatever);
        if ($found < $this->spec)
        {
            $this->addErrorInternal($object, 
                            $attribute, 
                            'special', 
                            array('found' => $found, 'required' => $this->spec)
            );
        }

        // digit characters
        $found = preg_match_all('![\d]!', $value, $whatever);
        if ($found < $this->digit)
        {
            $this->addErrorInternal($object, 
                            $attribute, 
                            'digit', 
                            array('found' => $found, 'required' => $this->digit)
            );
        }
    }

    /**
    * Adds an error about the specified attribute to the active record.
    * This is a helper method that call addError which performs message selection and internationalization.
    * 
    * Construct the message and the params array to call addError().
    * 
    * @param CModel $object the data object being validated
    * @param string $attribute the attribute being validated
    * @param string $tested_param the tested property (eg 'upper case') for generating the error message
    * @param array $values values for the placeholders :is and :should in the error message - array(['found'] => <int>, ['required'] => <int>)
     */
    private function addErrorInternal($object, $attribute,$tested_param, array $values)
    {   
        $this->message = Yii::t( $this->i18ncategory, $this->message ); 

	if ($tested_param!="")
	    $tested_param = Yii::t( $this->i18ncategory, $tested_param );
	    
        $params = array('{attribute}' => $attribute, '{tested_param}' => $tested_param, '{found}' => $values['found'], '{required}' => $values['required']);
        parent::addError($object, $attribute, $this->message, $params);
    }
    
}

?>
