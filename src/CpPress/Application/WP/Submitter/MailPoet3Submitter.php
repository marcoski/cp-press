<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\WP\Submitter;


use Commonhelp\App\Http\Request;
use Commonhelp\Util\Inflector;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;
use MailPoet\API\API;
use MailPoet\Models\Form;
use MailPoet\Models\Subscriber;

class MailPoet3Submitter extends Submitter
{

    /** @var \MailPoet\API\MP\v1\API */
    private $api;

    public function __construct(Request $request, Filter $filter, Hook $hook)
    {
        parent::__construct($request, $filter, $hook);

        $this->api = API::MP('v1');
    }

    protected function submit()
    {
        $type = Inflector::camelize($this->request->getParam('_cppress-mailpoet_type'));
        $id = $this->request->getParam('_cppress-mailpoet-id');
        $list = intval($this->request->getParam('_cppress_mailpoet-list', 2));
        $submit = 'submit' . $type;

        /** @var Form $form */
        $form = Form::getPublished()->findOne($this->request->getParam('_cppress-mailpoet-id', 1));

        $lists = [];
        foreach($this->api->getLists() as $mlist){
            if($mlist['id'] == $this->request->getParam('_cppress_mailpoet-list')){
                $lists[] = $mlist['id'];
            }
        }

        if(empty($lists)){
            return array('valid' => false, 'message' => __('Invalid or empty list', 'cppress'));
        }

        if($form === false && !$form->validate()){
            return array('valid' => false, 'message' => __('Invalid or empty form', 'cppress'));
        }

        $subscriberData = array();
        foreach($form->getFieldList() as $field){
            $fieldValue = $this->request->getParam('cppress-mailpoet-'.$field);
            if($field === 'email' && !sanitize_email($fieldValue)){
                return array('valid' => false, 'message' => __('Invalid or empty email address', 'cppress'));
            }
            $subscriberData[$field] = sanitize_text_field($fieldValue);
        }



        /** TODO Bug on Mailpoet API? On handling existents subscriber? */
        if(Subscriber::findOne($subscriberData['email']) === false){
            try{
                $this->api->addSubscriber($subscriberData, $lists, ['send_confirmation_email' => false, 'schedule_welcome_email' => false]);
                return array('valid' => 'true');
            }catch(\Exception $e) {
                return array('valid' => 'true');
            }
        }


        return array('valid' => 'true');
    }

    public function ajaxSubmit($instance, $args){
        return $this->submit();
    }

    public function nonajaxSubmit($instance, $args){
        return $this->submit();
    }
}