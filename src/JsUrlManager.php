<?php
namespace dmirogin\js\urlmanager;


use Yii;
use yii\base\BootstrapInterface;
use yii\base\Object;
use yii\helpers\Json;
use yii\base\Application;
use yii\web\JsExpression;
use yii\web\UrlManager;
use yii\web\UrlRule;
use yii\web\View;

/**
 * JsUrlManager allows you register a frontend UrlManager which almost is similar to yii\web\UrlManager
 * And provide configuration
 *
 * @author Dmitry Dorogin <dmirogin@ya.ru>
 */
class JsUrlManager extends Object implements BootstrapInterface
{
    /**
     * The location to register configuration Frontend UrlManager string
     * @var int
     */
    public $configurationStringPosition = View::POS_HEAD;

    /**
     * Set configuration to document.urlManagerConfiguration
     * @var bool
     */
    public $configureThroughVariable = false;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $configuration = $this->defineConfiguration($app->urlManager);
        if ($this->configureThroughVariable) {
            $this->configureFrontendUrlManagerThroughVariable($configuration);
        } else {
            $this->configureFrontendUrlManager($configuration);
        }

        self::registerAssets();
    }

    /**
     * Register necessary assets
     */
    public static function registerAssets()
    {
        JsUrlManagerAsset::register(Yii::$app->view);
    }

    /**
     * Define configuration based on yii\web\UrlManager's configuration
     * @param UrlManager $urlManager
     * @return array
     */
    public function defineConfiguration(UrlManager $urlManager)
    {
        $rules = [];
        foreach ($urlManager->rules as $name => $rule) {
            if ($rule instanceof UrlRule) {
                $rules[] = [
                    'name' => $rule->name,
                    'route' => $rule->route,
                    'suffix' => $rule->suffix
                ];
            } else if (is_string($rule)) {
                $rules[] = [
                    'name' => $name,
                    'route' => $rule
                ];
            }

        }

        return [
            'enablePrettyUrl' => $urlManager->enablePrettyUrl,
            'showScriptName' => $urlManager->showScriptName,
            'suffix' => $urlManager->suffix,
            'rules' => $rules
        ];
    }

    /**
     * Register js string that configure frontend UrlManager
     * @param array $configuration
     */
    protected function configureFrontendUrlManager(array $configuration)
    {
        Yii::$app->view->registerJs(
            'UrlManager.configure(' . $this->prepareForFrontend($configuration) . ');',
            $this->configurationStringPosition
        );
    }

    /**
     * Register js string that configure frontend UrlManager
     * @param array $configuration
     */
    protected function configureFrontendUrlManagerThroughVariable(array $configuration)
    {
        Yii::$app->view->registerJs(
            'document.urlManagerConfiguration = ' . $this->prepareForFrontend($configuration) . ';',
            $this->configurationStringPosition
        );
    }

    /**
     * Prepare any data to frontend
     * @param mixed $value
     * @return string
     */
    private function prepareForFrontend($value) {
        return (string) new JsExpression(Json::encode($value));
    }
}