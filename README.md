# kaspi_integration

Вспомогательный модуль для  ocstore 2.3 интегриции с каспи.кз торговой площадки банка kaspi Казахстан.

Модуль опршивает опенкарт на наличие товара на складах и выдает их колличество и наличие. В последствии добавляет в одера. Модуль рабочий но еще довольно таки сырой .

Надо доработать взаимодествия с доставкой опенкарт сейчас она очень индивидуальна, требует вмешательства прогрммиста не посредственно в код файла . catalog/controller/extension/kaspi_integration.php

Установка. 
 1 Расппаковать файлы в корневую директорию
 2 добавить строки " $data['menus'][] = array(
					'id'       => '',
					'icon'	   => 'fa-cart-arrow-down', 
					'name'	   => 'kaspi.kz',
					'href'     => $this->url->link('extension/kaspi_integration', 'token=' . $this->session->data['token'], true),
					 'children' => array()
				);"

				в файл admin/controller/common/column_left.php
