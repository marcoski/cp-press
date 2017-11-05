<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\WP\Theme\Filter\Field;


use CpPress\Application\FrontEnd\FrontFilterController;
use CpPress\Application\FrontEndApplication;

class SimpleField extends AbstractField
{
    public function render()
    {
        $params = array(
            $this->filter->apply('cppress_filter_simple_input_label', __('Search', 'cppress')),
        );
        return FrontEndApplication::part(FrontFilterController::class, 'simple', $this->application->getContainer(), $params);
    }

}