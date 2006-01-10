CREATE TABLE `navi` (
`navi_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`parent_id` INT NOT NULL ,
`menu_order` INT NOT NULL ,
`item_type` CHAR( 1 ) NOT NULL ,
`item_name` VARCHAR( 255 ) NULL ,
`tooltip` VARCHAR( 255 ) NULL ,
`icon_url` VARCHAR( 255 ) NULL ,
`command` VARCHAR( 255 ) NULL 
);
