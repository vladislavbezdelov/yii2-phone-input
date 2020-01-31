<?php

namespace borales\extensions\phoneInput;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * Widget of the phone input
 * @package borales\extensions\phoneInput
 */
class PhoneInput extends InputWidget
{
    /** @var string HTML tag type of the widget input ("tel" by default) */
    public $htmlTagType = 'tel';
    /** @var array Default widget options of the HTML tag */
    public $defaultOptions = ['autocomplete' => "off",'class'=>'form-control'];
    /**
     * @link https://github.com/jackocnr/intl-tel-input#options More information about JS-widget options.
     * @var array Options of the JS-widget
     */
    public $jsOptions = [];
	/**
	 * @var bool
	 */
    public $autoFocus = true;

    public function init()
    {
        parent::init();
        PhoneInputAsset::register($this->view);
        $id = ArrayHelper::getValue($this->options, 'id');
        $jsOptions = $this->jsOptions ? Json::encode($this->jsOptions) : "";
        $jsInit = <<<JS
(function ($) {
    "use strict";
    $('#$id').intlTelInput($jsOptions);
})(jQuery);
JS;
        $this->view->registerJs($jsInit);
        if ($this->hasModel()) {
            $js = <<<JS
(function ($) {
    "use strict";
    $('#$id')
    .parents('form')
    .on('submit', function() {
        $('#$id')
        .val($('#$id')
        .intlTelInput('getNumber'));
    });
})(jQuery);
JS;
	        $this->view->registerJs($js);
            $jsFocus = <<<JS
window.onload = function() {
		document.getElementById('$id').focus();
	};
JS;
            if ($this->autoFocus) {
	            $this->view->registerJs($jsFocus);
            }

            $jsOnlyNumbers = <<<JS
document.getElementById('$id').onkeydown = function(event) {
	return (event.key >= '0' && event.key <= '9')
			|| event.key == '+'
			|| event.key == 'Backspace'
			|| event.key == 'Enter'
			|| event.key == '('
			|| event.key == ')';};
JS;
            $this->view->registerJs($jsOnlyNumbers);
        }
    }

    /**
     * @return string
     */
    public function run()
    {
        $options = ArrayHelper::merge($this->defaultOptions, $this->options);
        if ($this->hasModel()) {
            return Html::activeInput($this->htmlTagType, $this->model, $this->attribute, $options);
        }
        return Html::input($this->htmlTagType, $this->name, $this->value, $options);
    }
}
