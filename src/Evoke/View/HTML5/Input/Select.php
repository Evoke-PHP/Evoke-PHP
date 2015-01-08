<?php
/**
 * Select Input View
 *
 * @package View\HTML5\Input
 */
namespace Evoke\View\HTML5\Input;

use InvalidArgumentException;
use LogicException;
use Traversable;

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
     * Attributes for the select element.
     *
     * @var string[]
     */
    protected $attribs;

    /**
     * Field in the data for the option text.
     *
     * @var string
     */
    protected $fieldText;

    /**
     * Field in the data for the option value.
     *
     * @var string
     */
    protected $fieldValue;

    /**
     * Attributes for each select option.
     *
     * @var string[]
     */
    protected $optionAttribs;

    /**
     * Options for the select.
     *
     * @var mixed[]
     */
    protected $options;

    /**
     * Value that is selected or NULL for none.
     *
     * @var mixed|null
     */
    protected $selectedValue;

    /**
     * Construct a Select view.
     *
     * @param string   $fieldText     Field from the data for the option text.
     * @param string   $fieldValue    Field from the data for the option value.
     * @param string[] $attribs       Attributes for the select element.
     * @param string[] $optionAttribs Attributes for the option elements.
     */
    public function __construct(
        $fieldText,
        $fieldValue = 'ID',
        Array        $attribs = [],
        Array        $optionAttribs = []
    ) {
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
     * @throws LogicException If the option does not have the required fields.
     */
    public function get()
    {
        $optionElements = [];

        foreach ($this->options as $record) {
            if (!isset($record[$this->fieldText],
                $record[$this->fieldValue])
            ) {
                throw new LogicException(
                    'Option needs TextField: ' . $this->fieldText . ' and ValueField: ' . $this->fieldValue
                );
            }

            $optionAttribs = array_merge(
                $this->optionAttribs,
                ['value' => $record[$this->fieldValue]]
            );

            if (isset($this->selectedValue) &&
                $record[$this->fieldValue] == $this->selectedValue
            ) {
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
     * @param mixed[] $options The options to select between.
     * @throws InvalidArgumentException If the options aren't traversable.
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new InvalidArgumentException('needs traversable options.');
        }

        $this->options = $options;
    }

    /**
     * Set the value that has been selected from the options.
     *
     * @param mixed $selectedValue
     */
    public function setSelected($selectedValue)
    {
        $this->selectedValue = $selectedValue;
    }
}
// EOF
