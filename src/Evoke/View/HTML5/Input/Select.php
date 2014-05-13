<?php
/**
 * Select Input View
 *
 * @package View\HTML5\Input
 */
namespace Evoke\View\HTML5\Input;

use InvalidArgumentException,
    LogicException;

/**
 * Select Input View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class Select
{
    /**
     * Protected properties.
     *
     * @var string[]   $attribs       Attributes for the select element.
     * @var string     $fieldText     Field in the data for the option text.
     * @var string     $fieldValue    Field in the data for the option value.
     * @var string[]   $optionAttribs Attributes for each select option.
     * @var mixed[]    $options       Options for the select.
     * @var mixed|null $selectedValue Value that is selected or NULL for none.
     */
    protected $attribs, $fieldText, $fieldValue, $optionAttribs, $options,
        $selectedValue;

    /**
     * Construct a Select view.
     *
     * @param string   Field from the data for the option text.
     * @param string   Field from the data for the option value.
     * @param string[] Attributes for the select element.
     * @param string[] Attributes for the option elements.
     */
    public function __construct(/* String */ $fieldText,
                                /* String */ $fieldValue    = 'ID',
                                Array        $attribs       = [],
                                Array        $optionAttribs = [])
    {
        $this->attribs       = $attribs;
        $this->fieldText     = $fieldText;
        $this->fieldValue    = $fieldValue;
        $this->optionAttribs = $optionAttribs;
        $this->options       = [];
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the select element.
     *
     * @return mixed[] The select element.
     */
    public function get()
    {
        $optionElements = [];

        foreach ($this->options as $record)
        {
            if (!isset($record[$this->fieldText],
                       $record[$this->fieldValue]))
            {
                throw new LogicException(
                    'Option needs TextField: ' . $this->fieldText .
                    ' and ValueField: ' . $this->fieldValue);
            }

            $optionAttribs = array_merge(
                $this->optionAttribs,
                ['value' => $record[$this->fieldValue]]);

            if (isset($this->selectedValue) &&
                $record[$this->fieldValue] == $this->selectedValue)
            {
                $optionAttribs['selected'] = 'selected';
            }

            $optionElements[] =
                ['option', $optionAttribs, $record[$this->fieldText]];
        }

        return ['select', $this->attribs, $optionElements];
    }

    /**
     * Set the options that we are selecting between.
     *
     * @param mixed[] The options to select between.
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable)
        {
            throw new InvalidArgumentException('needs traversable options.');
        }

        $this->options = $options;
    }

    /**
     * Set the value that has been selected from the options.
     *
     * @param mixed The selected value.
     */
    public function setSelected($selectedValue)
    {
        $this->selectedValue = $selectedValue;
    }
}
// EOF
