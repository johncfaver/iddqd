--
-- PostgreSQL database dump
--

-- Dumped from database version 9.1.11
-- Dumped by pg_dump version 9.1.11
-- Started on 2014-01-22 16:25:18 EST

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

CREATE DATABASE iddqddb WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';

ALTER DATABASE iddqddb OWNER TO iddqd;

\connect iddqddb

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

CREATE TABLE moldata (
    moldataid integer NOT NULL,
    molid integer NOT NULL,
    authorid integer NOT NULL,
    dateadded timestamp without time zone,
    targetid integer,
    value real,
    datatype integer NOT NULL
);


ALTER TABLE public.moldata OWNER TO iddqd;

CREATE SEQUENCE bindingdata_bindingdataid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE public.bindingdata_bindingdataid_seq OWNER TO iddqd;

ALTER SEQUENCE bindingdata_bindingdataid_seq OWNED BY moldata.moldataid;

CREATE TABLE bounties (
    bountyid integer NOT NULL,
    targetid integer,
    placed_by_id integer NOT NULL,
    pursued_by_id integer,
    molid integer,
    claimed boolean DEFAULT false NOT NULL,
    date_posted timestamp without time zone NOT NULL,
    date_pursued timestamp without time zone,
    date_claimed timestamp without time zone
);


ALTER TABLE public.bounties OWNER TO iddqd;

CREATE SEQUENCE bounties_bountyid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bounties_bountyid_seq OWNER TO iddqd;

ALTER SEQUENCE bounties_bountyid_seq OWNED BY bounties.bountyid;

CREATE TABLE bountycomments (
    bountycommentid integer NOT NULL,
    bountyid integer NOT NULL,
    bountycomment text NOT NULL,
    dateadded timestamp without time zone NOT NULL,
    authorid integer NOT NULL
);


ALTER TABLE public.bountycomments OWNER TO iddqd;

CREATE SEQUENCE bountycomments_bountycommentid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bountycomments_bountycommentid_seq OWNER TO iddqd;

ALTER SEQUENCE bountycomments_bountycommentid_seq OWNED BY bountycomments.bountycommentid;

CREATE TABLE datacomments (
    datacommentid integer NOT NULL,
    dataid integer NOT NULL,
    authorid integer NOT NULL,
    dateadded timestamp without time zone NOT NULL,
    datacomment text NOT NULL
);


ALTER TABLE public.datacomments OWNER TO iddqd;

CREATE SEQUENCE datacomment_datacommentid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.datacomment_datacommentid_seq OWNER TO iddqd;

ALTER SEQUENCE datacomment_datacommentid_seq OWNED BY datacomments.datacommentid;

CREATE TABLE datatypes (
    datatypeid integer NOT NULL,
    type text NOT NULL,
    units text NOT NULL
);


ALTER TABLE public.datatypes OWNER TO iddqd;

CREATE SEQUENCE datatypes_datatypeid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.datatypes_datatypeid_seq OWNER TO iddqd;

ALTER SEQUENCE datatypes_datatypeid_seq OWNED BY datatypes.datatypeid;


CREATE TABLE invites (
    email text NOT NULL,
    datesent timestamp without time zone NOT NULL,
    invitekey text NOT NULL,
    datejoined timestamp without time zone
);


ALTER TABLE public.invites OWNER TO iddqd;

CREATE TABLE molcomments (
    molcommentid integer NOT NULL,
    molid integer NOT NULL,
    molcomment text NOT NULL,
    dateadded timestamp without time zone NOT NULL,
    authorid integer NOT NULL
);


ALTER TABLE public.molcomments OWNER TO iddqd;

CREATE SEQUENCE molcomments_molcommentid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.molcomments_molcommentid_seq OWNER TO iddqd;

ALTER SEQUENCE molcomments_molcommentid_seq OWNED BY molcomments.molcommentid;


CREATE TABLE molecules (
    molname text,
    authorid integer,
    dateadded timestamp without time zone,
    molweight real,
    molid integer NOT NULL,
    molformula text,
    iupac text,
    cas text
);


ALTER TABLE public.molecules OWNER TO iddqd;

CREATE SEQUENCE molecules_molid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.molecules_molid_seq OWNER TO iddqd;

ALTER SEQUENCE molecules_molid_seq OWNED BY molecules.molid;

CREATE TABLE passwordchanges (
    userid integer NOT NULL,
    daterequested timestamp without time zone NOT NULL,
    changekey text NOT NULL,
    datechanged timestamp without time zone,
    changed boolean DEFAULT false NOT NULL
);


ALTER TABLE public.passwordchanges OWNER TO iddqd;

CREATE TABLE targets (
    nickname text NOT NULL,
    fullname text,
    targetid integer NOT NULL,
    targetclass text,
    series text,
    authorid integer,
    dateadded timestamp without time zone
);

ALTER TABLE public.targets OWNER TO iddqd;

CREATE SEQUENCE targets_targetid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE public.targets_targetid_seq OWNER TO iddqd;

ALTER SEQUENCE targets_targetid_seq OWNED BY targets.targetid;

CREATE TABLE tokens (
    userid integer NOT NULL,
    token text NOT NULL,
    startdate timestamp without time zone NOT NULL,
    enddate timestamp without time zone NOT NULL
);

ALTER TABLE public.tokens OWNER TO iddqd;

CREATE TABLE users (
    userid integer NOT NULL,
    username text NOT NULL,
    password text NOT NULL,
    email text NOT NULL,
    isadmin boolean DEFAULT false NOT NULL
);

ALTER TABLE public.users OWNER TO iddqd;

CREATE SEQUENCE users_userid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE public.users_userid_seq OWNER TO iddqd;

ALTER SEQUENCE users_userid_seq OWNED BY users.userid;

ALTER TABLE ONLY bounties ALTER COLUMN bountyid SET DEFAULT nextval('bounties_bountyid_seq'::regclass);

ALTER TABLE ONLY bountycomments ALTER COLUMN bountycommentid SET DEFAULT nextval('bountycomments_bountycommentid_seq'::regclass);

ALTER TABLE ONLY datacomments ALTER COLUMN datacommentid SET DEFAULT nextval('datacomment_datacommentid_seq'::regclass);

ALTER TABLE ONLY datatypes ALTER COLUMN datatypeid SET DEFAULT nextval('datatypes_datatypeid_seq'::regclass);

ALTER TABLE ONLY molcomments ALTER COLUMN molcommentid SET DEFAULT nextval('molcomments_molcommentid_seq'::regclass);

ALTER TABLE ONLY moldata ALTER COLUMN moldataid SET DEFAULT nextval('bindingdata_bindingdataid_seq'::regclass);

ALTER TABLE ONLY molecules ALTER COLUMN molid SET DEFAULT nextval('molecules_molid_seq'::regclass);

ALTER TABLE ONLY targets ALTER COLUMN targetid SET DEFAULT nextval('targets_targetid_seq'::regclass);

ALTER TABLE ONLY users ALTER COLUMN userid SET DEFAULT nextval('users_userid_seq'::regclass);

ALTER TABLE ONLY moldata
    ADD CONSTRAINT bindingdatakey PRIMARY KEY (moldataid);

ALTER TABLE ONLY moldata
    ADD CONSTRAINT bindingdataunique UNIQUE (moldataid);

ALTER TABLE ONLY bounties
    ADD CONSTRAINT bounties_pkey PRIMARY KEY (bountyid);

ALTER TABLE ONLY bountycomments
    ADD CONSTRAINT bountycomments_pkey PRIMARY KEY (bountycommentid);

ALTER TABLE ONLY passwordchanges
    ADD CONSTRAINT changkeyindex PRIMARY KEY (changekey);

ALTER TABLE ONLY datacomments
    ADD CONSTRAINT datacomentkey PRIMARY KEY (datacommentid);

ALTER TABLE ONLY datatypes
    ADD CONSTRAINT datatypeskey PRIMARY KEY (datatypeid);

ALTER TABLE ONLY invites
    ADD CONSTRAINT invitesprimarykey PRIMARY KEY (invitekey);

ALTER TABLE ONLY molcomments
    ADD CONSTRAINT molcommentkey PRIMARY KEY (molcommentid);

ALTER TABLE ONLY molecules
    ADD CONSTRAINT molidkey PRIMARY KEY (molid);

ALTER TABLE ONLY molecules
    ADD CONSTRAINT molidunique UNIQUE (molid);

ALTER TABLE ONLY targets
    ADD CONSTRAINT targetidkey PRIMARY KEY (targetid);

ALTER TABLE ONLY targets
    ADD CONSTRAINT targetidunique UNIQUE (targetid);

ALTER TABLE ONLY tokens
    ADD CONSTRAINT tokenskey PRIMARY KEY (userid);

ALTER TABLE ONLY users
    ADD CONSTRAINT useridkey PRIMARY KEY (userid);


REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2014-01-22 16:25:18 EST

--
-- PostgreSQL database dump complete
--

INSERT INTO datatypes VALUES (1, 'IC50', 'µM');
INSERT INTO datatypes VALUES (2, 'EC50', 'µM');
INSERT INTO datatypes VALUES (3, 'kd', 'µM');
INSERT INTO datatypes VALUES (4, 'CC50', 'µM');
INSERT INTO datatypes VALUES (5, 'Aq. Sol.', 'g/L');
INSERT INTO datatypes VALUES (6, 'H NMR', 'file');
INSERT INTO datatypes VALUES (7, 'C NMR', 'file');
INSERT INTO datatypes VALUES (8, 'Mass Spec.', 'file');
INSERT INTO datatypes VALUES (9, 'Synthesis', 'file');
INSERT INTO datatypes VALUES (10, 'Manuscript', 'file');
INSERT INTO datatypes VALUES (11, 'Structure ', 'file');
INSERT INTO datatypes VALUES (13, 'Image', 'file');
INSERT INTO datatypes VALUES (15, 'Other', 'file');

SELECT pg_catalog.setval('datatypes_datatypeid_seq', 15, true);


