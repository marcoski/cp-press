<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets;


use Commonhelp\App\Http\JsonResponse;
use Commonhelp\App\Http\Output;

abstract class CpWidgetBaseCustom extends CpWidgetBase
{

    private $customAction;

    private $ajaxData;

    public function __construct($name, array $widget_options = array(), array $control_options = array(), array $templateDirs = array())
    {
        $this->customAction = null;
        $this->ajaxData = [];
        $templateDirs =  array( get_template_directory() . '/', get_stylesheet_directory() . '/' );
        parent::__construct($name, $widget_options, $control_options, $templateDirs);
    }

    protected function render()
    {
        $template = 'template-parts/widgets/'.$this->getAction();
        if($this->customAction !== null){
            $template = 'template-parts/widgets/'.$this->id_base.'/'.$this->customAction;
        }
        return $this->template->inc($template, $this->vars);
    }

    protected function renderAjax(){
        $template = 'template-parts/widgets/'.$this->id_base.'/'.$this->customAction;
        /** @var Output $io */
        $jsonData = array(
            'what'		=> $this->getAppName(),
            'action'	=> $this->customAction,
            'data'		=> $this->template->inc($template, $this->vars)
        );
        if(!empty($this->wpAjaxData)){
            $jsonData['extra'] = $this->ajaxData;
        }

        $response = new JsonResponse($jsonData);
        echo $response->render();
        exit;
    }

    protected function setCustomAction($customAction){
        $this->customAction = $customAction;
    }

    protected function setAjaxData($ajaxData){
        if(is_array($ajaxData)){
            $this->ajaxData = $ajaxData;
        }else{
            $this->ajaxData[] = $ajaxData;
        }
    }
}