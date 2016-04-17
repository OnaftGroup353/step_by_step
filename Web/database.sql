/*
	BEGIN article_types TABLE
*/
CREATE SEQUENCE article_types_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE article_types
(
  id integer NOT NULL DEFAULT nextval('article_types_id_seq'::regclass),
  name text NOT NULL,
  CONSTRAINT article_types_pkey PRIMARY KEY (id)
)

INSERT INTO article_types (name) VALUES ('Article');
INSERT INTO article_types (name) VALUES ('Text');
INSERT INTO article_types (name) VALUES ('Table');
INSERT INTO article_types (name) VALUES ('Picture');
INSERT INTO article_types (name) VALUES ('Code');
INSERT INTO article_types (name) VALUES ('Video');
/*
	END article_types TABLE
*/


/*
	BEGIN scope TABLE
*/
CREATE SEQUENCE scope_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE scope
(
  id bigint NOT NULL DEFAULT nextval('scope_id_seq'::regclass),
  name text NOT NULL,
  CONSTRAINT scope_pkey PRIMARY KEY (id)
)

INSERT INTO scope (name) VALUES ('User');
INSERT INTO scope (name) VALUES ('Moderator');
INSERT INTO scope (name) VALUES ('Administrator');
/*
	END scope TABLE
*/

/*
	BEGIN social_network_types TABLE
*/
CREATE SEQUENCE social_network_types_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE social_network_types
(
  id bigint NOT NULL DEFAULT nextval('social_network_types_id_seq'::regclass),
  name text NOT NULL,
  CONSTRAINT social_network_types_pkey PRIMARY KEY (id)
)

INSERT INTO social_network_types (name) VALUES ('Vkontakte');
INSERT INTO social_network_types (name) VALUES ('Facebook');
/*
	END social_network_types TABLE
*/

/*
	BEGIN users TABLE
*/
CREATE SEQUENCE users_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE users
(
  id bigint NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  email text NOT NULL,
  password text NOT NULL,
  scope_id bigint NOT NULL,
  first_name text,
  middle_name text,
  last_name text,
  interest text,
  "position" text,
  social_network_type bigint,
  social_network_id text,
  isbanned boolean NOT NULL,
  CONSTRAINT users_pkey PRIMARY KEY (id),
  CONSTRAINT users_scope_fkey FOREIGN KEY (scope_id)
      REFERENCES scope (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT users_social_network_fkey FOREIGN KEY (social_network_type)
      REFERENCES social_network_types (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
/*
	END users TABLE
*/

/*
	BEGIN articles TABLE
*/
CREATE SEQUENCE articles_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE articles
(
  id bigint NOT NULL DEFAULT nextval('articles_id_seq'::regclass),
  caption text NOT NULL,
  article_type_id bigint NOT NULL,
  content text,
  update_date timestamp without time zone NOT NULL,
  previous_version_article_id bigint,
  author_id bigint NOT NULL,
  isdeleted boolean NOT NULL,
  CONSTRAINT articles_pkey PRIMARY KEY (id),
  CONSTRAINT articles_article_types_fkey FOREIGN KEY (article_type_id)
      REFERENCES article_types (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT articles_users_fkey FOREIGN KEY (author_id)
      REFERENCES users (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
/*
	END articles TABLE
*/

/*
	BEGIN manuals TABLE
*/
CREATE SEQUENCE manuals_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE manuals
(
  id bigint NOT NULL DEFAULT nextval('manuals_id_seq'::regclass),
  name text NOT NULL,
  parent_branch_id bigint,
  update_date timestamp without time zone NOT NULL,
  isdeleted boolean NOT NULL,
  CONSTRAINT manuals_pkey PRIMARY KEY (id)
)
/*
	END manuals TABLE
*/

/*
	BEGIN manual_authors TABLE
*/
CREATE SEQUENCE manual_authors_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
  
 CREATE TABLE manual_authors
(
  id bigint NOT NULL DEFAULT nextval('manual_authors_id_seq'::regclass),
  manual_id bigint NOT NULL,
  author_id bigint NOT NULL,
  CONSTRAINT manual_authors_pkey PRIMARY KEY (id),
  CONSTRAINT manual_authors_manuals_fkey FOREIGN KEY (manual_id)
      REFERENCES manuals (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT manual_authors_users_fkey FOREIGN KEY (author_id)
      REFERENCES users (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
/*
	END manual_authors TABLE
*/

/*
	BEGIN manual_articles TABLE
*/
CREATE SEQUENCE manual_articles_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
  
CREATE TABLE manual_articles
(
  id bigint NOT NULL DEFAULT nextval('manual_articles_id_seq'::regclass),
  manual_id bigint NOT NULL,
  article_id bigint NOT NULL,
  parent_article_id bigint,
  CONSTRAINT manual_articles_pkey PRIMARY KEY (id),
  CONSTRAINT manual_articles_articles_fkey FOREIGN KEY (article_id)
      REFERENCES articles (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT manual_articles_manuals_fkey FOREIGN KEY (manual_id)
      REFERENCES manuals (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
/*
	END manual_articles TABLE
*/