<?php


namespace Meta\BusinessExtension\Model\System\Message;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Notification\MessageInterface;
use Meta\BusinessExtension\Model\ResourceModel\MetaIssueNotification;

class ConflictingModulesNotification implements MessageInterface
{
    /**
     * @var MetaIssueNotification
     */
    private MetaIssueNotification $metaIssueNotification;

    /**
     * @var ModuleManager
     */
    private ModuleManager $moduleManager;

    /**
     * @var array
     */
    private static array $conflictingModules = ['Apptrian_MetaPixelApi'];

    /**
     * @var string
     */
    private static string $meta_business_extension = 'Meta_BusinessExtension';

    /**
     * @var string
     */
    private string $conflictingModulesFound = '';

    /**
     * ConflictingModulesNotification constructor.
     *
     * @param MetaIssueNotification $metaIssueNotification
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        MetaIssueNotification      $metaIssueNotification,
        ModuleManager $moduleManager
    ) {
        $this->metaIssueNotification = $metaIssueNotification;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Get identity
     *
     * @return string
     */
    public function getIdentity()
    {
        $notification = $this->metaIssueNotification->loadVersionNotification();
        return $notification['notification_id'] ?? '';
    }

    /**
     * Validate if any conflicting modules are found
     *
     * @return bool
     */
    public function isDisplayed()
    {
        //find out if the user enabled the Meta Business Extension
        $has_Meta_BusinessExtension = $this->moduleManager->isEnabled(self::$meta_business_extension);

        //iterate through the user's module manager to see if they have any conflicting modules
        foreach (self::$conflictingModules as $conflictingModule) {
            if ($this->moduleManager->isEnabled($conflictingModule) && $has_Meta_BusinessExtension) {
                $this->conflictingModulesFound = $conflictingModule;
                return true;
            }
        }
        return false;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return sprintf('The following module conflicts with the Facebook & Instagram Extension: [%s].
            Please disable the conflicting module.', $this->conflictingModulesFound);
    }

    /**
     * Get severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
