<?php
class CaptchaComponent extends Component {
    public $components = array('Session');
    public $settings = array();

    /**
     * Default values for settings.
     * - operand: the operand used in the math equation
     * - minNumber: the minimum number used to generate the random variables.
     * - maxNumber: the corresponding maximum number.
     * - numberOfVariables: the number of variables to include in the equation.
     *
     * @access private
     * @var array
     */
    private $__defaults = array(
        'operand' => '+',
        'minNumber' => 2,
        'maxNumber' => 3,
        'numberOfVariables' => 2
    );

    /**
     * The variables used in the equation.
     *
     * @access public
     * @var array
     */
    public $variables = array();

    /*
     * The math equation.
     *
     * @access public
     * @var string
     */
    public $equation = null;

    /**
     * Configuration method.
     *
     * @access public
     * @param object $model
     * @param array $settings
     */
    public function initialize(Controller $controller, $settings = array()) {
        $this->settings = array_merge($this->__defaults, $settings);
    }

    /*
     * Method that generates a math equation based on the component settings. It also calls
     * a secondary function, registerAnswer(), which determines the answer to the equation
     * and sets it as a session variable.
     *
     * @access public
     * @return string
     *
     */

    function random_numbers($digits) {
        $min = pow(10, $digits - 1);
        $max = pow(10, $digits) - 1;
        return mt_rand($min, $max);
    }

    public function generateEquation( $params = '' ) {
        $this->equation = $this->random_numbers(5);
        // This function determines the answer to the equation and stores it as a session variable.
        $this->registerAnswer( $params );

        return $this->equation;
    }

    /*
     * Determines the answer to the math question from the variables set in generateEquation()
     * and registers it as a session variable.
     *
     * @access public
     * @return integer
     */
    public function registerAnswer( $params ) {
        // The eval() function gives us the $answer variable.
        eval("\$answer = ".$this->equation.";");

        $this->Session->write('MathCaptcha.answer'.$params, $answer);

        return $answer;
    }

    /*
     * Compares the given data to the registered equation answer.
     *
     * @access public
     * @return boolean
     */
    public function validates( $data, $params = '' ) {
        return $data == $this->Session->read('MathCaptcha.answer'.$params);
    }

}

?>
