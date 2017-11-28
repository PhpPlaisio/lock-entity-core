/*================================================================================*/
/* DDL SCRIPT                                                                     */
/*================================================================================*/
/*  Title    : ABC-Framework: Core Entity Lock                                    */
/*  FileName : abc-lock-entity-core.ecm                                           */
/*  Platform : MySQL 5.6                                                          */
/*  Version  :                                                                    */
/*  Date     : zaterdag 25 november 2017                                          */
/*================================================================================*/
/*================================================================================*/
/* CREATE TABLES                                                                  */
/*================================================================================*/

CREATE TABLE ABC_LOCK_ENTITY_NAME (
  ltn_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  ltn_name VARCHAR(200) NOT NULL,
  ltn_label VARCHAR(200) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  CONSTRAINT PK_ABC_LOCK_ENTITY_NAME PRIMARY KEY (ltn_id)
);

CREATE TABLE ABC_LOCK_ENTITY (
  ltt_id INT UNSIGNED AUTO_INCREMENT NOT NULL,
  cmp_id SMALLINT UNSIGNED NOT NULL,
  ltn_id SMALLINT UNSIGNED NOT NULL,
  ltt_entity_id BIGINT UNSIGNED NOT NULL,
  ltt_version BIGINT UNSIGNED NOT NULL,
  CONSTRAINT PK_ABC_LOCK_ENTITY PRIMARY KEY (ltt_id)
)
engine=innodb;

/*
COMMENT ON COLUMN ABC_LOCK_ENTITY.ltn_id
The ID of the lock type.
*/

/*
COMMENT ON COLUMN ABC_LOCK_ENTITY.ltt_entity_id
The ID of the entity.
*/

/*
COMMENT ON COLUMN ABC_LOCK_ENTITY.ltt_version
The version of the entity lock.
*/

/*================================================================================*/
/* CREATE INDEXES                                                                 */
/*================================================================================*/

CREATE UNIQUE INDEX IX_ABC_LOCK_ENTITY_NAME1 ON ABC_LOCK_ENTITY_NAME (ltn_name);

CREATE UNIQUE INDEX IX_ABC_LOCK_ENTITY1 ON ABC_LOCK_ENTITY (cmp_id, ltn_id, ltt_entity_id);

/*================================================================================*/
/* CREATE FOREIGN KEYS                                                            */
/*================================================================================*/

ALTER TABLE ABC_LOCK_ENTITY
  ADD CONSTRAINT FK_ABC_LOCK_ENTITY_AUT_COMPANY
  FOREIGN KEY (cmp_id) REFERENCES ABC_AUTH_COMPANY (cmp_id);

ALTER TABLE ABC_LOCK_ENTITY
  ADD CONSTRAINT FK_ABC_LOCK_ENTITY_ABC_LOCK_ENTITY_NAME
  FOREIGN KEY (ltn_id) REFERENCES ABC_LOCK_ENTITY_NAME (ltn_id);
