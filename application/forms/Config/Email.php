<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Forms_Config
 * @version         SVN: $Rev$
 * @author          $Author$
 */

/**
 * Create form to input e-mail data in configuration
 *
 * @package         MySQLDumper
 * @subpackage      Forms_Config
 */
class Application_Form_Config_Email extends Zend_Form_SubForm
{
    /**
     * Language translator
     * @var Msd_Language
     */
    protected $_lang;

    /**
     * Configuration
     * @var Msd_Configuration
     */
    protected $_config;

    /**
     * Add elements to form.
     *
     * @return void
     */
    public function init()
    {
        $this->_config = Msd_Configuration::getInstance();
        $this->_lang = Msd_Language::getInstance();
        $this->setDisableLoadDefaultDecorators(true);
        $this->setDecorators(array('SubForm'));
        $this->addDisplayGroupPrefixPath(
            'Msd_Form_Decorator',
            'Msd/Form/Decorator/'
        );
        $this->setDisplayGroupDecorators(array('DisplayGroup'));
        $this->_addActivateButton();
        $this->_addSender();
        $this->_addRecipient();
        $this->_addButtonAddRecipientCc();

        // add Recipients CC
        $ccElements = $this->_setRecipientCc(
            $this->_config->get('config.email.RecipientCc')
        );

        $this->_addAttachement();
        $this->_addEmailProgram();

        // create and add display group
        $elements = array(
                'sendEmail',
                'SenderAddress',
                'SenderName',
                'RecipientAddress',
                'RecipientName',
                'AddCc');
        // add cc-recipients
        if (count($ccElements)>0) {
            $elements = array_merge(
                $elements,
                $ccElements
            );
        }
        $elements = array_merge(
            $elements,
            array(
                'attachBackup',
                'Maxsize',
                'MaxsizeUnit',
                'Program'
            )
        );
        $this->addDisplayGroup(
            $elements,
            'email',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('DisplayGroup'),
                'legend' => $this->_lang->getTranslator()->_('L_CONFIG_EMAIL')
            )
        );

        $elements = array(
            'SendmailCall'
        );
        $this->addDisplayGroup(
            $elements,
            'sendmailConfig',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('DisplayGroup'),
                'legend' => $this->_lang->getTranslator()->_('L_SENDMAIL'),
                'class' => 'sendmailConfig',
            )
        );

        $elements = array(
            'SMTPHost',
            'SMTPPort',
            'SMTPUserAuth',
            'SMTPUser',
            'SMTPPassword',
            'SMTPUseSSL',
        );
        $this->addDisplayGroup(
            $elements,
            'smtpConfig',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('DisplayGroup'),
                'legend' => $this->_lang->getTranslator()->_('L_SMTP'),
                'class' => 'smtpConfig',
            )
        );
    }

    /**
     * Add e-mail activate button to form
     *
     * @return void
     */
    private function _addActivateButton()
    {
        //Button send email
        $this->addElement(
            'radio',
            'sendEmail',
            array(
                'class' => 'radio toggler',
                'label' => 'L_SEND_MAIL_FORM',
                'onclick' => "myToggle(this, 'y', 'emailToggle');",
                'listsep' => ' ',
                'disableLoadDefaultDecorators' => true,
                'multiOptions' => array(
                    'y' => 'L_YES',
                    'n' => 'L_NO',
                ),
                'decorators' => array('Default'),
            )
        );
    }
    /**
     * Add Cc-Recipients
     *
     * @param array $configRecipientCc All Cc-Recipients
     *
     * @return array Element names to add to display group
     */
    private function _setRecipientCc($recipientsCc)
    {
        if ($recipientsCc === null) {
            return;
        }

        if (count($recipientsCc) == 0) {
            return;
        }

        $elements = array();
        //$i = 0;
        foreach ($recipientsCc as $i => $recipient) {
            //Recipient CC email
            $this->addElement(
                'text',
                'RecipientCc_'.$i.'_Address',
                array(
                    'class' => 'text emailToggle',
                    'label' => 'L_EMAIL_CC',
                    'listsep' => ' ',
                    'size' => 30,
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => array('LineStart'),
                    'validators' => array('EmailAddress'),

                )
            );

            //CC name
            $this->addElement(
                'text',
                'RecipientCc_'.$i.'_Name',
                array(
                    'class' => 'text emailToggle',
                    'label' => 'L_NAME',
                    'listsep' => ' ',
                    'size' => 30,
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => array('LineMiddle'),
                )
            );

            //button delete recipient
            $confirmDelete = sprintf(
                $this->_lang->getTranslator()->_('L_CONFIRM_RECIPIENT_DELETE'),
                $recipient['Name']
            );
            $confirmDelete = Msd_Html::getJsQuote($confirmDelete, true);
            $this->addElement(
                'button',
                'DeleteCc_'.$i,
                array(
                    'disableLoadDefaultDecorators' => true,
                    'content' =>
                        $this->getView()->getIcon('delete') . ' ' .
                        $this->_lang->getTranslator()->_('L_DELETE'),
                    'decorators' => array('LineEnd'),
                    'escape' => false,
                    'label' => '',
                    'class' => 'Formbutton emailToggle',
                    'onclick' =>
                        'if (!confirm(\'Ha\')) return false;'
                        .' deleteEmailReciptientCc(\'' . $i .'\');',
                )
            );

            $elements = array_merge(
                $elements,
                array(
                    'RecipientCc_'.$i.'_Address',
                    'RecipientCc_'.$i.'_Name',
                    'DeleteCc_'.$i,
                )
            );
            $i++;
        }
        return $elements;
    }

    /**
     * Adds line with sender inputs
     *
     * @return void
     */
    private function _addSender()
    {
        //Sender email
        $this->addElement(
            'text',
            'SenderAddress',
            array(
                'class' => 'text emailToggle',
                'label' => 'L_EMAIL_SENDER',
                'size' => 30,
                'listsep' => ' ',
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('LineStart'),
                'validators' => array(
                    new Zend_Validate_NotEmpty(Zend_Validate_NotEmpty::STRING),
                    'EmailAddress'
                ),
            )
        );

        //Sender name
        $this->addElement(
            'text',
            'SenderName',
            array(
                'class' => 'text emailToggle',
                'label' => 'L_NAME',
                'size' => 30,
                'listsep' => ' ',
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('LineEnd'),
                'validators' => array('NotEmpty'),
            )
        );
    }

    /**
     * Add button to add a cc-recipient
     *
     * @return void
     */
    private function _addButtonAddRecipientCc()
    {
        $this->addElement(
            'button',
            'AddCc',
            array(
                'disableLoadDefaultDecorators' => true,
                'content' =>
                    $this->getView()->getIcon('plus') . ' ' .
                    $this->_lang->getTranslator()->_('L_ADD_RECIPIENT'),
                'decorators' => array('Default'),
                'escape' => false,
                'label' => '',
                'class' => 'Formbutton emailToggle',
                'onclick' => "addEmailReciptientCc();",
            )
        );
    }
    /**
     * Add line with recipient name and e-mail
     *
     * @return void
     */
    private function _addRecipient()
    {

        //Recipient email
        $this->addElement(
            'text',
            'RecipientAddress',
            array(
                'class' => 'text emailToggle',
                'label' => 'L_EMAIL_RECIPIENT',
                'size' => 30,
                'listsep' => ' ',
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('LineStart'),
                'validators' => array('EmailAddress'),
            )
        );

        //Recipient name
        $this->addElement(
            'text',
            'RecipientName',
            array(
                'class' => 'text emailToggle',
                'label' => 'L_NAME',
                'size' => 30,
                'listsep' => ' ',
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('LineEnd'),
            )
        );
    }

    /**
     * Add line attachement and -size to form
     *
     * @return void
     */
    private function _addAttachement()
    {

        //Attach backup
        $this->addElement(
            'radio',
            'attachBackup',
            array(
                'class' => 'radio emailToggle toggler',
                'label' => 'L_ATTACH_BACKUP',
                'onclick' => "myToggle(this, 'y', 'attachToggle');",
                'listsep' => ' ',
                'disableLoadDefaultDecorators' => true,
                'multiOptions' => array(
                    'y' => 'L_YES',
                    'n' => 'L_NO',
                ),
                'decorators' => array('Default'),
            )
        );

        //Max filesize
        $this->addElement(
            'text',
            'Maxsize',
            array(
                'class' => 'text right emailToggle attachToggle',
                'size' => 6,
                'maxlength' => 6,
                'label' => 'L_EMAIL_MAXSIZE',
                'listsep' => ' ',
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('LineStart'),
                'validators' => array('Digits'),
            )
        );

        //Max filesize unit
        $this->addElement(
            'select',
            'MaxsizeUnit',
            array(
                'class' => 'select emailToggle attachToggle',
                'listsep' => ' ',
                'multiOptions' => array(
                    'kb' => 'L_UNIT_KB',
                    'mb' => 'L_UNIT_MB',
                    ),
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('LineEnd'),
            )
        );
    }

    /**
     * Add select for e-mail program
     *
     * @return void
     */
    private function _addEmailProgram()
    {
        //Mail program
        $this->addElement(
            'select',
            'Program',
            array(
                'class' => 'select emailToggle',
                'label' => 'L_MAILPROGRAM',
                'id' => 'toggleEmailSettings',
                'listsep' => ' ',
                'multiOptions' => array(
                    'php' => 'L_PHPMAIL',
                    'sendmail' => 'L_SENDMAIL',
                    'smtp' => 'L_SMTP'
                ),
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Default'),
                'onchange' => 'toggleEmailProgram();',
            )
        );
        $this->addElement(
            'text',
            'SendmailCall',
            array(
                'class' => 'text',
                'label' => $this->_lang->getTranslator()->_('L_CALL'),
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Default'),
            )
        );
        $this->addElement(
            'text',
            'SMTPHost',
            array(
                'class' => 'text',
                'label' => $this->_lang->getTranslator()->_('L_SMTP_HOST'),
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Default'),
            )
        );
        $this->addElement(
            'text',
            'SMTPPort',
            array(
                'class' => 'text',
                'label' => $this->_lang->getTranslator()->_('L_SMTP_PORT'),
                'validators' => array('Digits'),
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Default'),
            )
        );
        $this->addElement(
            'radio',
            'SMTPUserAuth',
            array(
                'class' => 'radio toggler',
                'label' => $this->_lang->getTranslator()->_('L_AUTHORIZE'),
                'listsep' => ' ',
                'multiOptions' => array(
                    'y' => 'L_YES',
                    'n' => 'L_NO',
                ),
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Default'),
                'onclick' => "myToggle(this, 'y', 'SMTPAuthToggle');",
            )
        );
        $this->addElement(
            'text',
            'SMTPUser',
            array(
                'class' => 'text SMTPAuthToggle',
                'label' => $this->_lang->getTranslator()->_('L_USERNAME'),
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Default'),
                'rowclass' => 'SMTPAuth',
            )
        );
        $this->addElement(
            'text',
            'SMTPPassword',
            array(
                'class' => 'text SMTPAuthToggle',
                'label' => $this->_lang->getTranslator()->_('L_PASSWORD'),
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Default'),
                'rowclass' => 'SMTPAuth',
            )
        );
        $this->addElement(
            'radio',
            'SMTPUseSSL',
            array(
                'class' => 'radio',
                'label' => $this->_lang->getTranslator()->_('L_USE_SSL'),
                'listsep' => ' ',
                'multiOptions' => array(
                    'y' => 'L_YES',
                    'n' => 'L_NO',
                ),
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Default'),
            )
        );
    }

    /**
     * Extracts an multi array from flat element names and values
     *
     * @param array $data The values to extract index and values from
     *
     * @return array The converted array
     */
    public function getValidValues($data)
    {
        $values = parent::getValidValues($data, true);
        $newArray = array();
        while (false !== (list($key, $value) = each($values))) {
            if (substr($key, 0, 18) != 'email_RecipientCc_') {
                continue;
            }
            list($prefix, $index, $fieldKey) = explode('_', $key);
            if (!isset($values['email_Recipient'])) {
                $values[$prefix] = array();
            }
            if (!isset($newArray[$index])) {
                $newArray[$index] = array();
            }
            $newArray[$index][$fieldKey] = $value;
            unset($values[$key]);
        }

        $values['email_RecipientCc'] = array_values($newArray);

        return $values;
    }

    /**
     * Set default values
     *
     * @param $defaults
     *
     * @return Zend_Form
     */
    public function setDefaults($defaults)
    {
        if (isset($defaults['email']['RecipientCc'])) {
            $recipientCc = array();
            while (false !== (list($recipientCcId, $recipientCcData) =
                each($defaults['email']['RecipientCc']))) {
                foreach ($recipientCcData as $key => $value) {
                    $recipientCc['RecipientCc_' .
                        $recipientCcId . '_' . $key] = $value;
                }
            }
            unset($defaults['email']['RecipientCc']);
            $defaults['email'] = array_merge(
                $defaults['email'],
                $recipientCc
            );
        }
        return parent::setDefaults($defaults);
    }
    /**
     * Set input default value
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function setDefault($name, $value)
    {
        $name = str_replace('.', '_', $name);
        parent::setDefault($name, $value);
    }

}