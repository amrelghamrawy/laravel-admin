<?php

namespace Encore\Admin\Form\Field;

/**
 * Class ListBox.
 *
 * @see https://github.com/istvan-ujjmeszaros/bootstrap-duallistbox
 */
class Listbox extends MultipleSelect
{
    protected $settings = [];

    public function settings(array $settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Set listbox height.
     *
     * @param int $height
     *
     * @return Listbox
     */
    public function height($height = 200)
    {
        return $this->settings(['selectorMinimalHeight' => $height]);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadRemoteOptions($url, $parameters = [], $options = [])
    {
        $options = json_encode(array_merge([
            'url' => $url.'?'.http_build_query($parameters),
        ], $options));

        $this->script = <<<SCRIPT

$.ajax($options).done(function(data) {

  var listbox = $("{$this->getElementClassSelector()}");

    var value = listbox.data('value') + '';

    if (value) {
      value = value.split(',');
    }

    for (var key in data) {
        var selected =  ($.inArray(key, value) >= 0) ? 'selected' : '';
        listbox.append('<option value="'+key+'" '+selected+'>'+data[key]+'</option>');
    }

    listbox.bootstrapDualListbox('refresh', true);
});
SCRIPT;

        return $this;
    }

    public function render()
    {
        $settings = array_merge([
            'infoText'              => trans('admin.listbox.text_total'),
            'infoTextEmpty'         => trans('admin.listbox.text_empty'),
            'infoTextFiltered'      => trans('admin.listbox.filtered'),
            'filterTextClear'       => trans('admin.listbox.filter_clear'),
            'filterPlaceHolder'     => trans('admin.listbox.filter_placeholder'),
            'selectorMinimalHeight' => 200,
        ], $this->settings);

        $settings = json_encode($settings);

        $this->script .= <<<SCRIPT

$("{$this->getElementClassSelector()}").bootstrapDualListbox($settings);

SCRIPT;

        $this->attribute('data-value', implode(',', (array) $this->value()));

        admin_assets('duallistbox');

        return parent::render();
    }
}
