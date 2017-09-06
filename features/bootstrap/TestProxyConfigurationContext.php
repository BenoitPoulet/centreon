<?php

use Centreon\Test\Behat\CentreonContext;
use Centreon\Test\Behat\Administration\ParametersCentreonUiPage;

class TestProxyConfigurationContext extends CentreonContext 
{
    private $page;
    private $wrongAddress = 'squad';
    private $wrongProxyPort = '9999';
    
    /**
     * @When I test the proxy configuration in the interface
     */
    public function ItestTheProxyConfigurationInTheInterface()
    {
        $this->page = new ParametersCentreonUiPage($this);
        $proxyUrl = $this->page->getProperty('proxy_url');
        $proxyPort = $this->page->getProperty('proxy_port');
        if ($proxyUrl !== 'squid' && $proxyPort !== '3128') {
            throw new \Exception('The proxy URL and/or port has not been saved properly');
        }
        $this->visit('main.php?p=50110&o=general');
        $this->assertFind('css', 'input[name="test_proxy"]')->click();
    }
    
    /**
     * @Then a popin displays a successful connexion
     */
    public function APopinDisplaysASuccessfulConnexion()
    {
        $this->spin(
            function ($context) {
                return $context->getSession()->getPage()->has(
                    'css',
                    'span[class="msg-field success2"]'
                );
            },
            'The pop-in did not showed up'
        );
        $value = $this->assertFind('css', 'span[class="msg-field success2"]')->getText();
        if ($value !== 'Connection Successful') {
            throw new \Exception('The URL to reach failed');
        } 
    }
    
    /**
     * @Given a Centreon user on the Centreon UI page with a proxy url and port wrongly configured
     */
    public function aCentreonUserOnTheCentreonUIpageWithAproxyUrlAndPortWronglyConfigured()
    {   
        $this->page = new ParametersCentreonUiPage($this);
        $this->page->setProperties(array(
            'proxy_url'=> $this->wrongAddress,
            'proxy_port'=> $this->wrongProxyPort
        ));
    }
    
    /**
     * @When I click on the test configuration button
     */
    public function IclickOnTheTestConfigurationButton()
    {
        $this->assertFind('css', 'input[name="test_proxy"]')->click();
    }
    
    /**
     * @Then a popin displays an error message
     */
    public function aPopinDisplaysAnErrorMessage()
    {
        $this->spin(
            function ($context) {
                return $context->getSession()->getPage()->has(
                    'css',
                    'span[class="msg-field error"]'
                );
            },
            'The pop-in did not showed up'
        );
        $value = $this->assertFind('css', 'span[class="msg-field error"]')->getText();
        if ($value == 'Connection Successful') {
            throw new \Exception('The Proxy configuration is incorrect');
        } 
    } 
}
