--------------------------------------------------------------
**YandexFood**
=====================================
Добро пожаловать на наш проект сайта по доставке еды! Здесь вы найдете информацию о нашей команде, технологиях, которые мы используем, и дополнительные ресурсы для начала работы.

--------------------------------------------------------------
**Используемые технологии**
=====================================
*	`MySQL - 8.0`: Система управления базами данных.
*	`HTML`: стандартизированный язык гипертекстовой разметки документов для просмотра веб-страниц в браузере.
*	`CSS`: язык стилей для документов HTML.
*	`PHP`: серверный язык сценариев для веб-разработки.
*	`Bootstrap`: свободный набор инструментов для создания сайтов и веб-приложений. Включает в себя HTML- и CSS-шаблоны оформления для типографики, веб-форм, кнопок, меток, блоков навигации и прочих компонентов веб-интерфейса, включая JavaScript-расширения.
--------------------------------------------------------------
**Команда**
=====================================
*	Маслаков Юрий
*   Тешкикна Анастасия

--------------------------------------------------------------
**Как запустить проект?**
=====================================

Чтобы начать использовать наш проект в собственных целях вам необходимо сделать несколько простых шагов:
1. Установите репозиторий удобным для себя способом.
2. Установите и запустите [OpenServer](https://ospanel.io/ "Привет!").
3. В настройках поставьте модули PHP_7.4 и MySQL-8.0-Win10.
4. Используя MyPhpAdminer содайте базу данных delivery и импортируйте в неё файл delivery.sql.
5. Извлечь файлы репозитория в localhost.
6. Запустите проект, используя панель OpenServer.
   
--------------------------------------------------------------
**Возможности нашего приложения**
=====================================
Мы разработали систему доставки еды.
Внутри неё есть четрые основые роли user,courier,manager,cook.
У каждого из них имеются свои права и возможности.
Для всех пользователей есть форма входа и регистрации, роли назначаются с помощью базы данных.
* User - может видеть меню, добавлять товары в корзину, оформлять заказ указывая свои данные(адрес, номер телефона, комментарий, время доставки).
* Manager - может создаёт новые позиции в меню, видит статусы всех заказов и может ими управлять, радактирует записи в меню.
* Курьер - может принимать заказ в работу, выбирать из предложеного списка заказов, связываться с менеджером.
* Cook - может принимать заказы в работу, отдавать заказы курьеру, видит какой курьер забрал заказ.
Для всех пользователей нашего сервиса определены зоны влияния.
Внутри файла delivery.sql уже есть пользователи с именами (Менеджер, курьер, повар) с одинаковым паролем для всех 123, необходимо войти в аккаунт, чтобы увидеть функционал соответствующего пользователя.  