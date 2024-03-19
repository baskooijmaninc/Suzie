<?phpnamespace KooijmanInc\Suzie\FormBuilder;use KooijmanInc\Suzie\Exception\NotSupported;use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollectorInterface;use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;use KooijmanInc\Suzie\FormBuilder\FormParts\Input\InputInterface;use KooijmanInc\Suzie\FormValidation\FormValidationInterface;use KooijmanInc\Suzie\Helper\Common;use KooijmanInc\Suzie\Object\FormObject\ObjectStorage;use KooijmanInc\Suzie\SuzieInterface;use Symfony\Contracts\Translation\TranslatorInterface;use Symfony\Component\HttpFoundation\RequestStack;#[\AllowDynamicProperties]abstract class AbstractFormBuilder implements FormBuilderInterface{    /**     * @var string     */    protected string $uuid;    /**     * @var SuzieInterface     */    protected SuzieInterface $suzie;    /**     * @var FormCollectorInterface     */    protected FormCollectorInterface $formCollector;    /**     * @var RequestStack     */    protected RequestStack $requestStack;    /**     * @var TranslatorInterface     */    protected TranslatorInterface $translator;    /**     * @var string|null     */    protected ?string $locale = null;    /**     * @var array     */    protected array $toBeSetInputs = [];    /**     * @var FormInterface     */    protected FormInterface $form;    /**     * @var     */    protected $formElements;    protected $formElementsValidation;    /**     * @var string     */    protected string $completeForm;    /**     * @param SuzieInterface $suzie     * @param TranslatorInterface $translator     * @param RequestStack $requestStack     */    public function __construct(SuzieInterface $suzie, TranslatorInterface $translator, RequestStack $requestStack)    {        $this->uuid = uniqid(str_replace('\\', '-', get_class($this)) . '-', true);        $this->suzie = $suzie;        $this->translator = $translator;        $this->requestStack = $requestStack;        $this->locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);    }    public function getUuid(): string    {        return $this->uuid;    }    /**     * @return FormInterface     */    public function getForm(): FormInterface    {        $this->form = $this->formCollector->form;        return $this->form;    }    public function getCompleteForm()    {        if (!isset($this->completeForm)) {            $this->setCompleteForm();        }        return $this->completeForm;    }    public function getFormElements()    {        return $this->formElements;    }    public function setFormElements(array $elements)    {        $this->formElements = $this->formCollector->formElements($elements);        return $this;    }    public function getFormElementsValidation()    {        return $this->formElementsValidation;    }    public function setFormElementsValidation(array $baseElements = [])    {        $this->formElementsValidation = $this->formCollector->formElementsValidation($this->form, $this->formElements, $baseElements);        return $this;    }    public function setElements(...$elements)    {        if (!isset($this->form)) {            $this->form = $this->getForm();            if (isset($elements[0]['action']) && ($this->form->action === null || $this->form->action === '')) {                $this->form->setAction($elements[0]['action']);            }        }        foreach ($this->formElements as $element => $value) {            $showElement[] = $element;            if (isset($this->form->showElements[1]) && (!in_array($element, $this->form->showElements) && $element !== 'button')) {                $this->formElements->$element->showElement(false);                unset($this->$element);            } else {                $this->formElements->$element->showElement(true);                $this->$element = $this->setFormElement($this->formElements->$element);            }        }        if (!isset($this->form->showElements)) {            $this->form->showElements($showElement);            $this->setFormElementsValidation();        }        return $this;    }    public function getRules()    {        $where = null;        $isValidated = true;        $toHandleElements = $this->form->getShowElements();        foreach ($toHandleElements as $elements) {            if ($this->formElementsValidation->{$elements}->name === $elements) {                $isValidated = $this->formElementsValidation->{$elements}->isValidated;                $where .= " {$this->formElementsValidation->{$elements}->logicalOperator} {$elements}{$this->formElementsValidation->{$elements}->comparisonOperator}?";                $value = $this->formElements->{$elements}->value;                if ($this->formElementsValidation->{$elements}->hashValue !== null) {                    if ($this->formElementsValidation->{$elements}->hashValue === 'encrypt') {                        $value = Common::encrypt($value);                    } elseif ($this->formElementsValidation->{$elements}->hashValue === 'sha1') {                        $value = SHA1($value);                    } elseif ($this->formElementsValidation->{$elements}->hashValue === 'md5') {                        $value = MD5($value);                    }                }                $bind[] = $value;                $logicalOperator[] = "{$this->formElementsValidation->{$elements}->logicalOperator} ";            }        }        $where = (isset($logicalOperator)) ? ltrim($where, reset($logicalOperator)) : null;        return ($where !== null && isset($bind) && $isValidated === true) ? [$where, $bind] : false;    }    public function hasRecord(): bool    {        return $this->suzie->hasRecord($this)[0] ?? false;    }    public function getRecord(): bool|array    {        return (isset($this->suzie->hasRecord($this)[0])) ? $this->suzie->hasRecord($this)[1] : false;    }    public function &__get(string $name)    {        $accessor = "get" . ucfirst($name);        if (method_exists($this, $accessor) && is_callable([$this, $accessor])) {            $value = $this->$accessor();            return $value;        } elseif (property_exists($this, $name)) {            return $this->$name;        }        throw new NotSupported("__get: property or method ".get_called_class()."::{$name} is not supported");    }    /**     * @param $name     * @param $value     * @return mixed     * @throws NotSupported     */    public function __set($name, $value)    {        $accessor = "set" . ucfirst($name);        if (method_exists($this, $accessor) && is_callable([$this, $accessor])) {            return $this->$accessor($value);        } elseif (property_exists($this, $name)) {            return $this->$name = $value;        } elseif (!property_exists($this, $name) && (array_key_exists($name, $this->toBeSetInputs) || $name === 'button')) {            return $this->$name = $value;        }        throw new NotSupported("__set: property or method ".get_called_class()."::{$name} is not supported");    }    public function __isset(string $name)    {        if (property_exists($this, $name)) {            return true;        }        return false;    }    /**     * @param array $inputs     * @return void     */    public function toBeSetInputs(array $inputs): void    {        foreach ($inputs as $key => $value) {            if ($key === 'id') {                $key = 'protectedId';            }            if (!isset($this->toBeSetInputs[$key])) {                $this->toBeSetInputs[$key] = $value;            }        }    }    public function getFormInput(...$input)    {        $input = $input[0] ?? [];        if ($this->form->elementSize === 'sm' ?? $this->form->elementSize === 'lg') {            $input['class'] .= " form-control-{$this->form->elementSize}";        }        $return = "<input";            $return .= ($input['id']) ? " id=\"".Common::encrypt($input['id'])."\"" : null;            $return .= ($input['name']) ? " name=\"".Common::encrypt($input['name'])."\"" : null;            $return .= ($input['placeholder']) ? " placeholder=\"{$this->translator->trans($input['placeholder'], [], $input['domain'], $this->locale)}\"" : null;            $return .= ($input['value']) ? " value=\"{$input['value']}\"" : null;            $return .= ($input['type']) ? " type=\"{$input['type']}\"" : null;            $return .= (isset($input['disabled'])) ? " disabled aria-disabled=\"{$input['disabled']}\"" : null;        $return .= " class=\"{$input['class']}\">";        $return .= $this->hasFeedbackIcon($this->formElementsValidation->{$input['name']} ?? null);        return $return;    }    public function getFormSelect(...$select)    {        $select = $select[0] ?? [];        if ($this->form->elementSize === 'sm' ?? $this->form->elementSize === 'lg') {            $select['class'] .= " form-select-{$this->form->elementSize}";        }        $return = "<select";            $return .= ($select['id']) ? " id=\"".Common::encrypt($select['id'])."\"" : null;            $return .= ($select['name']) ? " name=\"".Common::encrypt($select['name'])."\"" : null;            $return .= " class=\"form-select";            $return .= ($select['class']) ? "{$select['class']}" : null;        $return .= ">";        $return .= "</select>";        $return .= $this->hasFeedbackIcon($this->formElementsValidation->{$input['name']} ?? null);        return $return;    }    public function save(bool $validate = true): bool    {        dump($this);        return false;    }    /**     * @param ...$button     * @return string     */    public function getFormButton(...$button): string    {        $button = $button[0] ?? [];        if ($this->form->elementSize === 'sm' ?? $this->form->elementSize === 'lg') {            $button['class'] .= " btn-{$this->form->elementSize}";        }        $return = "<button";            $return .= ($button['name']) ? " name=\"".Common::encrypt($button['name'])."\"" : null;            $return .= ($button['value']) ? " value=\"{$button['value']}\"" : null;        $return .= " class=\"{$button['class']}\">";            $return .= $this->translator->trans($button['displayName'], [], $button['domain'], $this->locale);        $return .= "</button>";        return $return;    }    /**     * @return $this     */    public function setCompleteForm(): static    {        $this->completeForm = '';        if ($this->form->showElements === []) {            $this->form->showElements(array_keys($this->toBeSetInputs));        }        if ($this->form->standardForm === true) {            $this->completeForm = "<form action=\"".htmlspecialchars($this->form->action)."\" method=\"{$this->form->method}\"{$this->form->encType}{$this->form->target}{$this->form->autoComplete}{$this->form->acceptCharset}{$this->form->name}{$this->form->rel}{$this->form->novalidate}>";                foreach ($this->form->showElements as $element) {                if (property_exists($this->formElements, $element)) {                    $this->completeForm .= $this->hasFeedbackMessage($this->formElementsValidation->{$element} ?? null);                    $this->completeForm .= "<div class=\"mb-3{$this->hasLabel($this->formElements->$element)}{$this->hasFeedback($this->formElementsValidation->{$element} ?? null)}\">";                        $inputElement = $this->setFormElement($this->formElements->$element);                        $this->completeForm .= $inputElement;                    $this->completeForm .= "</div>";                }            }                $this->completeForm .= "<div class=\"mb-3\">";                    $this->completeForm .= "<div class=\"{$this->hasLabel($this->formElements->button)}\">";                        $inputElement = $this->setFormElement($this->formElements->button);                        $this->completeForm .= $inputElement;                    $this->completeForm .= "</div>";                $this->completeForm .= "</div>";            $this->completeForm .= "</form>";        } else {            if ($this->form->useFormMainElement === true) {                $this->completeForm = "<form action=\"".htmlspecialchars($this->form->action)."\" method=\"{$this->form->method}\"{$this->form->encType}{$this->form->target}{$this->form->autoComplete}{$this->form->acceptCharset}{$this->form->name}{$this->form->rel}{$this->form->novalidate}>";            }            foreach ($this->form->showElements as $element) {                if (property_exists($this->formElements, $element)) {                    $this->completeForm .= $this->hasFeedbackMessage($this->formElementsValidation->{$element} ?? null);                    $this->completeForm .= "<div class=\"mb-3{$this->hasLabel($this->formElements->$element)}{$this->hasFeedback($this->formElementsValidation->{$element} ?? null)}\">";                    $inputElement = $this->setFormElement($this->formElements->$element);                    $this->completeForm .= $inputElement;                    $this->completeForm .= "</div>";                }            }            if ($this->form->useFormMainElement === true) {                $this->completeForm .= "</form>";            }        }        return $this;    }    /**     * @param string $name     * @param array $arguments     * @return mixed     * @throws NotSupported     */    public function __call(string $name, $arguments)    {        if (empty($arguments) && $this->__isset($name)) {            return $this->__get($name);        } elseif (!empty($arguments)) {            return $this->__set($name, $arguments);        }        throw new NotSupported("__call (".get_called_class()."::{$name}) with args: (".print_r($arguments, true).") is not supported.");    }    /**     * @return ObjectStorage     */    protected function createObjectStorageObject(): ObjectStorage    {        return new ObjectStorage($this->suzie);    }    /**     * @param InputInterface $input     * @return string|null     */    protected function setFormElement(InputInterface $input): string|null    {        $return = ($input->formElement !== 'button') ? $this->label($input) : null;        if ($input->formElement === 'input') {            $return .= $this->getFormInput($this->getInputAttributes($input));        } elseif ($input->formElement === 'select') {            $return .= $this->getFormSelect($this->getSelectAttributes($input));        } elseif ($input->formElement === 'button') {            $return .= $this->getFormButton($this->getButtonAttributes($input));        }        if ($input->showLabel === true && $this->form->showAllLabels !== false && $this->form->labelColWidth !== 0) {            $return .= "</div>";        }        return $return ?? null;    }    /**     * @param InputInterface $input     * @return string     */    protected function label(InputInterface $input): string    {        if ($this->form->showAllLabels === false) {            $class = "visually-hidden";            $div = null;        } elseif ($input->showLabel === true) {            if ($this->form->labelColWidth !== 0) {                $remain = 12 - $this->form->labelColWidth;                $class = "col-form-label col-sm-{$this->form->labelColWidth}";                $div = "<div class=\"col-sm-{$remain}\">";            } else {                $class = "form-label";                $div = null;            }        } else {            $class = "visually-hidden";            $div = null;        }        if ($input->displayName === null) {            $displayName = $input->name;        } else {            $displayName = $input->displayName;        }        if ($this->form->elementSize === 'sm' ?? $this->form->elementSize === 'lg') {            $class .= " form-control-{$this->form->elementSize}";        }        return "<label for=\"".Common::encrypt($input->name)."\" class=\"{$class} text-nowrap\">{$this->translator->trans($displayName, [], $input->domain, $this->locale)}:</label>{$div}";    }    /**     * @param InputInterface $input     * @return string|null     */    public function hasLabel(InputInterface $input): ?string    {        if ($this->form->showAllLabels === false) {            return null;        } elseif ($this->form->showAllLabels === true && $input->formElement === 'button') {            if ($this->form->labelColWidth !== 0) {                $remain = 12 - $this->form->labelColWidth;                return "col-sm-{$remain} offset-sm-{$this->form->labelColWidth}";            }        } elseif ($input->showLabel === true && $this->form->labelColWidth !== 0) {            return " row";        }        return null;    }    /**     * @param InputInterface $input     * @return array     */    protected function getInputAttributes(InputInterface $input): array    {        $return = ['id' => $input->name, 'name' => $input->name, 'class' => $input->class, 'domain' => $input->domain];        if ($this->form->showAllPlaceholders === true) {            $return['placeholder'] = $input->displayName ?? $input->name;        }        if ($this->form->showAllValues === true) {            if ($input->value === 'auto_increment') {                $return['value'] = '0';            } else {                $return['value'] = $input->value;            }        }        if ($input->elementType !== null) {            $return['type'] = $input->elementType;        }        if ($input->lockedValue === true) {            $return['disabled'] = "true";        }        return $return;    }    /**     * @param InputInterface $input     * @return array     */    protected function getSelectAttributes(InputInterface $input): array    {        return ['id' => $input->name, 'name' => $input->name, 'domain' => $input->domain];    }    /**     * @param InputInterface $input     * @return array     */    protected function getButtonAttributes(InputInterface $input): array    {        $return = ['id' => $input->name, 'name' => $input->name, 'displayName' => $input->displayName ?? $input->name, 'class' => $input->class, 'domain' => $input->domain];        if ($this->form->showAllValues === true) {            $return['value'] = $input->value;        }        return $return;    }    protected function hasFeedback(FormValidationInterface $element = null)    {        if ($element !== null) {            if ($element->hasError === true) {                return " has-error has-feedback";            }            if ($element->hasWarning === true) {                return " has-warning has-feedback";            }            if ($element->hasSuccess === true) {                return " has-success has-feedback";            }        }        return null;    }    protected function hasFeedbackIcon(FormValidationInterface $element = null)    {        if ($element !== null) {            if ($element->hasError === true) {                return "<i class=\"fa fa-times form-control-feedback\"></i>";            }            if ($element->hasWarning === true) {                return "<i class=\"fa fa-exclamation-triangle form-control-feedback\"></i>";            }            if ($element->hasSuccess === true) {                return "<i class=\"fa fa-check form-control-feedback\"></i>";            }        }        return null;    }    protected function hasFeedbackMessage(FormValidationInterface $element = null)    {        if ($element !== null) {            if ($element->hasError === true && $element->errorMessage !== null) {                return "<div class=\"alert alert-danger\">$element->errorMessage</div>";            }            if ($element->hasWarning === true) {                return "<div class=\"alert alert-warning\">$element->errorMessage</div>";            }            if ($element->hasSuccess === true) {                //return $element->errorMessage;            }        }        return null;    }}