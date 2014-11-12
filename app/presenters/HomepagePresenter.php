<?php

namespace App\Presenters;

use Nette,
	App\Model,
	Nette\Application\UI;

class HomepagePresenter extends BasePresenter {

	private $databaseOne = ['1', '2', '3'];

	private $databaseTwo = [
		['11', '12', '13'],
		['22', '23', '24'],
		['33', '34', '35'],
	];

	private $databaseThree = [
		[
			['111', '112', '113'],
			['121', '122', '123'],
			['131', '132', '133'],
		],
		[
			['222', '223', '224'],
			['232', '233', '234'],
			['242', '243', '244'],
		],
		[
			['333', '334', '335'],
			['343', '344', '345'],
			['353', '354', '355'],
		],
	];

	public function beforeRender() {
		parent::beforeRender();
		$this->template->_form = $this['form']; // form {snippet} workaround
	}

	protected function createComponentForm($name) {
		$form = new UI\Form;
		$this[$name] = $form;

		$form->addSelect('one', 'One', $this->databaseOne)->setDefaultValue(1);
		//dump($form['one']->value);

		$form->addSelect('two', 'Two', $this->databaseTwo[$form['one']->value])->setDefaultValue(1);
		$form->addSelect('three', 'Three', $this->databaseThree[$form['two']->value][1])->setDefaultValue(1);

		$form->addSubmit('send', 'Odeslat');
		$form->onSuccess[] = $this->success;
		return $form;
	}

	public function success(UI\Form $form, $vals) {
		dump($vals);
	}

	public function handleInvalidateTwo($value) {
		$this['form']['two']->setItems($this->databaseTwo[$value]);
		$this['form']['three']->setItems($this->databaseThree[$value][1]);
		$this->redrawControl();
	}

	public function handleInvalidateThree($valueOne, $valueTwo) {
		$this['form']['three']->setItems($this->databaseThree[$valueOne][$valueTwo]);
		$this->redrawControl('three');
	}

}
