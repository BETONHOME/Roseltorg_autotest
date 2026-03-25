<?php

use Tests\Support\AcceptanceTester;

class RoseltorgTestCest
{

    private $tenderNumber = '0319300076426000189';
    private $expectedAmount = '117066';

    public function checkTender(AcceptanceTester $I)
    {
        //Открыть сайт торговой площадки
        $I->amOnPage('/');
        $I->wait(5);

        //Закрыть модальнок окно
        try{
            $I->click('//button[contains(text(), "Дальше")]');
            $I->wait(1);
            $I->click('//button[contains(text(), "Хорошо")]');
            $I->comment("Закрыл модальное окно");
        } catch (Exception $e) {
            $I->comment("Нет модального окна");
        }

        //Проверить что мы на нужном сайте
        $I->see('Росэлторг');

        //Перейти в раздел 44-ФЗ
        $I->click('//a[contains(text(), "44-ФЗ. Государственные закупки")]');
        $I->wait(2);
        $I->see('44-ФЗ');

        //Ввод номера тендера    
        $I->fillField('//input[@name="query_field"]', $this->tenderNumber);
        $I->wait(2);

        //Нажатие кнопки поиска
        $I->click('//button[@type="submit" and contains(@class, "search-box__submit")]');
        $I->wait(3);

        //Проверка модального окна, если оно не загрузилось на главной странице
        try{
            $I->click('//button[contains(text(), "Дальше")]');
            $I->wait(1);
            $I->click('//button[contains(text(), "Хорошо")]');
            $I->comment("Закрыл модальное окно");
        } catch (Exception $e) {
            $I->comment("Нет модального окна");
        }

        //Переход в карточку тендера
        $I->waitForElement("//a[contains(text(), '{$this->tenderNumber}')]", 5);
        $I->click("//a[contains(text(), '{$this->tenderNumber}')]");
        $I->wait(3);

        //Проверка, что найденный тендер корректен
        $I->see($this->tenderNumber);
        $I->comment("Номер тендера корректный");

        //Проверка суммы найденного тендера
        $amountText = $I->grabTextFrom('//div[@class="lot-item__sum"]/p');
        $I->comment("Найденная сумма: " . $amountText);

        $amountClean = str_replace(' ', '', $amountText);
        $amountClean = explode(',', $amountClean)[0];
        $I->comment("Сумма без копеек: " . $amountClean);

        //Сравнение суммы тендера с корректным
        $I->assertEquals($this->expectedAmount, $amountClean);
        $I->comment("Сумма тендера равна 117 066 руб.");

        //Нажатие кнопки "Подать заявку"
        $I->click('//a[contains(@class, "procedura-podat-zayavku") and text()="Подать заявку"]');
        $I->wait(3);

        //Проверка, что перешли на страницу с предупреждением
        $I->see('Обратите внимание, что перед началом работы в системе необходимо');
        $I->comment("Появилась страница с предупрждением");
        $I->wait(2);

        //Нажатие кнопки "Продолжить работу"
        $I->click('//a[normalize-space()="Продолжить работу"]');
        $I->comment("Нажал на 'Продолжить работу'");
        $I->wait(5);

        //Переход на открывшуюся страницу авторизации
        $I->switchToNextTab();
        $I->wait(10);

        //Проверка наличия кнопки с авторизацией на Госуслугах
        $I->see('ЕРУЗ (Госуслуги)');
    }

}