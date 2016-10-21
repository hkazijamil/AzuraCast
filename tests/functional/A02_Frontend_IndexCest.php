<?php
class A01_Frontend_IndexCest extends CestAbstract
{
    /**
     * @before setupComplete
     * @before login
     * @after cleanup
     */
    public function seeHomepage(FunctionalTester $I)
    {
        $I->wantTo('See the proper data on the homepage.');

        $I->amOnPage('/');
        $I->see('Dashboard');

        $I->see('Listeners Across All Stations');
        $I->see('Listeners Per Station');
        $I->see('Functional Test Radio');
    }
}