CREATE TABLE `ABC_AUTH_COMPANY` (
  `cmp_id` SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  `cmp_abbr` VARCHAR(15) NOT NULL,
  `cmp_label` VARCHAR(20) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  CONSTRAINT `PRIMARY_KEY` PRIMARY KEY (`cmp_id`)
);

insert into `ABC_AUTH_COMPANY`( `cmp_id`
,                               `cmp_abbr`
,                               `cmp_label` )
values( 1
,       'SYS'
,       'CMP_ID_SYS')
,      ( 2
,       'PLAISIO'
,       'CMP_ID_PLAISIO')
;

commit;
