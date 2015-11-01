<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;

class HomepagePresenter extends UI\Presenter {

	private $databaseOne;
	private $databaseTwo;
	private $databaseThree;
	private $databaseFour;
	private $databaseFive;

	public function __construct()
	{
		parent::__construct();
		$this->databaseOne = range(1, 5);
		$calculateNextDimension = function($previous) {
			array_walk_recursive($previous, function(&$item, $key) {
				$start = (int)($item . 1);
				$item = range($start, $start + 4);
			});
			return $previous;
		};
		$this->databaseTwo = $calculateNextDimension($this->databaseOne);
		$this->databaseThree = $calculateNextDimension($this->databaseTwo);
		$this->databaseFour = $calculateNextDimension($this->databaseThree);
		$this->databaseFive = $calculateNextDimension($this->databaseFour);
	}

	public function beforeRender() {
		parent::beforeRender();
		$this->template->_form = $this['form']; // form {snippet} workaround
	}

	protected function createComponentForm($name) {
		$form = new UI\Form($this, $name);

		$form->addSelect('one', 'One', $this->databaseOne)->setDefaultValue(1);
		$form->addSelect('two', 'Two', $this->databaseTwo[$form['one']->value])->setDefaultValue(1);
		$form->addSelect('three', 'Three', $this->databaseThree[$form['one']->value][$form['two']->value])->setDefaultValue(1);
		$form->addSelect('four', 'Four', $this->databaseFour[$form['one']->value][$form['two']->value][$form['three']->value])->setDefaultValue(1);
		$form->addSelect('five', 'Five', $this->databaseFive[$form['one']->value][$form['two']->value][$form['three']->value][$form['four']->value])->setDefaultValue(1);

		$form->addSubmit('send', 'Odeslat');
		$form->onSuccess[] = function($_, $vals) {
			dump($vals); //selected possitions
			dump([ //selected values
				$this->databaseOne[$vals['one']],
				$this->databaseTwo[$vals['one']][$vals['two']],
				$this->databaseThree[$vals['one']][$vals['two']][$vals['three']],
				$this->databaseFour[$vals['one']][$vals['two']][$vals['three']][$vals['four']],
				$this->databaseFive[$vals['one']][$vals['two']][$vals['three']][$vals['four']][$vals['five']],
			]);
		};
		return $form;
	}

	public function handleInvalidateTwo($value) {
		$this['form']['two']->setItems($this->databaseTwo[$value]);
		$this['form']['three']->setItems($this->databaseThree[$value][1]);
		$this['form']['four']->setItems($this->databaseFour[$value][1][1]);
		$this['form']['five']->setItems($this->databaseFive[$value][1][1][1]);
		$this->redrawControl('two');
		$this->redrawControl('three');
		$this->redrawControl('four');
		$this->redrawControl('five');
	}

	public function handleInvalidateThree($valueOne, $valueTwo) {
		$this['form']['three']->setItems($this->databaseThree[$valueOne][$valueTwo]);
		$this['form']['four']->setItems($this->databaseFour[$valueOne][$valueTwo][1]);
		$this['form']['five']->setItems($this->databaseFive[$valueOne][$valueTwo][1][1]);
		$this->redrawControl('three');
		$this->redrawControl('four');
		$this->redrawControl('five');
	}

	public function handleInvalidateFour($valueOne, $valueTwo, $valueThree) {
		$this['form']['four']->setItems($this->databaseFour[$valueOne][$valueTwo][$valueThree]);
		$this['form']['five']->setItems($this->databaseFive[$valueOne][$valueTwo][$valueThree][1]);
		$this->redrawControl('four');
		$this->redrawControl('five');
	}

	public function handleInvalidateFive($valueOne, $valueTwo, $valueThree, $valueFour) {
		$this['form']['five']->setItems($this->databaseFive[$valueOne][$valueTwo][$valueThree][$valueFour]);
		$this->redrawControl('five');
	}

}
