--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: iddqddb; Type: DATABASE; Schema: -; Owner: iddqd
--

CREATE DATABASE iddqddb WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';


ALTER DATABASE iddqddb OWNER TO iddqd;

\connect iddqddb

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: moldata; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

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

--
-- Name: bindingdata_bindingdataid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE bindingdata_bindingdataid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bindingdata_bindingdataid_seq OWNER TO iddqd;

--
-- Name: bindingdata_bindingdataid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE bindingdata_bindingdataid_seq OWNED BY moldata.moldataid;


--
-- Name: bounties; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

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

--
-- Name: bounties_bountyid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE bounties_bountyid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bounties_bountyid_seq OWNER TO iddqd;

--
-- Name: bounties_bountyid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE bounties_bountyid_seq OWNED BY bounties.bountyid;


--
-- Name: bountycomments; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE bountycomments (
    bountycommentid integer NOT NULL,
    bountyid integer NOT NULL,
    bountycomment text NOT NULL,
    dateadded timestamp without time zone NOT NULL,
    authorid integer NOT NULL
);


ALTER TABLE public.bountycomments OWNER TO iddqd;

--
-- Name: bountycomments_bountycommentid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE bountycomments_bountycommentid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bountycomments_bountycommentid_seq OWNER TO iddqd;

--
-- Name: bountycomments_bountycommentid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE bountycomments_bountycommentid_seq OWNED BY bountycomments.bountycommentid;


--
-- Name: datacomments; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE datacomments (
    datacommentid integer NOT NULL,
    dataid integer NOT NULL,
    authorid integer NOT NULL,
    dateadded timestamp without time zone NOT NULL,
    datacomment text NOT NULL
);


ALTER TABLE public.datacomments OWNER TO iddqd;

--
-- Name: datacomment_datacommentid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE datacomment_datacommentid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.datacomment_datacommentid_seq OWNER TO iddqd;

--
-- Name: datacomment_datacommentid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE datacomment_datacommentid_seq OWNED BY datacomments.datacommentid;


--
-- Name: datatypes; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE datatypes (
    datatypeid integer NOT NULL,
    type text NOT NULL,
    units text NOT NULL,
    class integer NOT NULL,
    CONSTRAINT class_check CHECK ((class = ANY (ARRAY[1, 2, 3])))
);


ALTER TABLE public.datatypes OWNER TO iddqd;

--
-- Name: COLUMN datatypes.class; Type: COMMENT; Schema: public; Owner: iddqd
--

COMMENT ON COLUMN datatypes.class IS '1 = Activity
2 = Property
3 = Document';


--
-- Name: datatypes_datatypeid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE datatypes_datatypeid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.datatypes_datatypeid_seq OWNER TO iddqd;

--
-- Name: datatypes_datatypeid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE datatypes_datatypeid_seq OWNED BY datatypes.datatypeid;


--
-- Name: invites; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE invites (
    email text NOT NULL,
    datesent timestamp without time zone NOT NULL,
    invitekey text NOT NULL,
    datejoined timestamp without time zone
);


ALTER TABLE public.invites OWNER TO iddqd;

--
-- Name: molcomments; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE molcomments (
    molcommentid integer NOT NULL,
    molid integer NOT NULL,
    molcomment text NOT NULL,
    dateadded timestamp without time zone NOT NULL,
    authorid integer NOT NULL
);


ALTER TABLE public.molcomments OWNER TO iddqd;

--
-- Name: molcomments_molcommentid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE molcomments_molcommentid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.molcomments_molcommentid_seq OWNER TO iddqd;

--
-- Name: molcomments_molcommentid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE molcomments_molcommentid_seq OWNED BY molcomments.molcommentid;


--
-- Name: molecules; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

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

--
-- Name: molecules_molid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE molecules_molid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.molecules_molid_seq OWNER TO iddqd;

--
-- Name: molecules_molid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE molecules_molid_seq OWNED BY molecules.molid;


--
-- Name: passwordchanges; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE passwordchanges (
    userid integer NOT NULL,
    daterequested timestamp without time zone NOT NULL,
    changekey text NOT NULL,
    datechanged timestamp without time zone,
    changed boolean DEFAULT false NOT NULL
);


ALTER TABLE public.passwordchanges OWNER TO iddqd;

--
-- Name: targetdata; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE targetdata (
    targetid integer NOT NULL,
    datatype integer DEFAULT 15,
    targetdatacomment text,
    authorid integer NOT NULL,
    targetdataid integer NOT NULL,
    dateadded timestamp without time zone
);


ALTER TABLE public.targetdata OWNER TO iddqd;

--
-- Name: targetdata_targetdataid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE targetdata_targetdataid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.targetdata_targetdataid_seq OWNER TO iddqd;

--
-- Name: targetdata_targetdataid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE targetdata_targetdataid_seq OWNED BY targetdata.targetdataid;


--
-- Name: targets; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

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

--
-- Name: targets_targetid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE targets_targetid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.targets_targetid_seq OWNER TO iddqd;

--
-- Name: targets_targetid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE targets_targetid_seq OWNED BY targets.targetid;


--
-- Name: tokens; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE tokens (
    userid integer NOT NULL,
    token text NOT NULL,
    startdate timestamp without time zone NOT NULL,
    enddate timestamp without time zone NOT NULL
);


ALTER TABLE public.tokens OWNER TO iddqd;

--
-- Name: users; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE users (
    userid integer NOT NULL,
    username text NOT NULL,
    password text NOT NULL,
    email text NOT NULL,
    isadmin boolean DEFAULT false NOT NULL
);


ALTER TABLE public.users OWNER TO iddqd;

--
-- Name: users_userid_seq; Type: SEQUENCE; Schema: public; Owner: iddqd
--

CREATE SEQUENCE users_userid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_userid_seq OWNER TO iddqd;

--
-- Name: users_userid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE users_userid_seq OWNED BY users.userid;


--
-- Name: bountyid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY bounties ALTER COLUMN bountyid SET DEFAULT nextval('bounties_bountyid_seq'::regclass);


--
-- Name: bountycommentid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY bountycomments ALTER COLUMN bountycommentid SET DEFAULT nextval('bountycomments_bountycommentid_seq'::regclass);


--
-- Name: datacommentid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY datacomments ALTER COLUMN datacommentid SET DEFAULT nextval('datacomment_datacommentid_seq'::regclass);


--
-- Name: datatypeid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY datatypes ALTER COLUMN datatypeid SET DEFAULT nextval('datatypes_datatypeid_seq'::regclass);


--
-- Name: molcommentid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY molcomments ALTER COLUMN molcommentid SET DEFAULT nextval('molcomments_molcommentid_seq'::regclass);


--
-- Name: moldataid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY moldata ALTER COLUMN moldataid SET DEFAULT nextval('bindingdata_bindingdataid_seq'::regclass);


--
-- Name: molid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY molecules ALTER COLUMN molid SET DEFAULT nextval('molecules_molid_seq'::regclass);


--
-- Name: targetdataid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY targetdata ALTER COLUMN targetdataid SET DEFAULT nextval('targetdata_targetdataid_seq'::regclass);


--
-- Name: targetid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY targets ALTER COLUMN targetid SET DEFAULT nextval('targets_targetid_seq'::regclass);


--
-- Name: userid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY users ALTER COLUMN userid SET DEFAULT nextval('users_userid_seq'::regclass);


--
-- Name: bindingdatakey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY moldata
    ADD CONSTRAINT bindingdatakey PRIMARY KEY (moldataid);


--
-- Name: bindingdataunique; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY moldata
    ADD CONSTRAINT bindingdataunique UNIQUE (moldataid);


--
-- Name: bounties_pkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY bounties
    ADD CONSTRAINT bounties_pkey PRIMARY KEY (bountyid);


--
-- Name: bountycomments_pkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY bountycomments
    ADD CONSTRAINT bountycomments_pkey PRIMARY KEY (bountycommentid);


--
-- Name: changkeyindex; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY passwordchanges
    ADD CONSTRAINT changkeyindex PRIMARY KEY (changekey);


--
-- Name: datacomentkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY datacomments
    ADD CONSTRAINT datacomentkey PRIMARY KEY (datacommentid);


--
-- Name: datatypeskey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY datatypes
    ADD CONSTRAINT datatypeskey PRIMARY KEY (datatypeid);


--
-- Name: invitesprimarykey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY invites
    ADD CONSTRAINT invitesprimarykey PRIMARY KEY (invitekey);


--
-- Name: molcommentkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY molcomments
    ADD CONSTRAINT molcommentkey PRIMARY KEY (molcommentid);


--
-- Name: molidkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY molecules
    ADD CONSTRAINT molidkey PRIMARY KEY (molid);


--
-- Name: molidunique; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY molecules
    ADD CONSTRAINT molidunique UNIQUE (molid);


--
-- Name: targetdatapkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY targetdata
    ADD CONSTRAINT targetdatapkey PRIMARY KEY (targetdataid);


--
-- Name: targetidkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targetidkey PRIMARY KEY (targetid);


--
-- Name: targetidunique; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targetidunique UNIQUE (targetid);


--
-- Name: tokenskey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY tokens
    ADD CONSTRAINT tokenskey PRIMARY KEY (userid);


--
-- Name: useridkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT useridkey PRIMARY KEY (userid);


--
-- Name: dataid_index; Type: INDEX; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE INDEX dataid_index ON datacomments USING btree (dataid);


--
-- Name: molid_index; Type: INDEX; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE INDEX molid_index ON moldata USING btree (molid);


--
-- Name: targetid_index; Type: INDEX; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE INDEX targetid_index ON targetdata USING btree (targetid);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

INSERT INTO datatypes VALUES (1, 'IC50', 'µM');
INSERT INTO datatypes VALUES (2, 'EC50', 'µM');
INSERT INTO datatypes VALUES (3, 'kd', 'µM');
INSERT INTO datatypes VALUES (4, 'CC50', 'µM');
INSERT INTO datatypes VALUES (5, 'Solubility', 'g/L');
INSERT INTO datatypes VALUES (6, 'H NMR', 'file');
INSERT INTO datatypes VALUES (7, 'C NMR', 'file');
INSERT INTO datatypes VALUES (8, 'Mass Spectrum', 'file');
INSERT INTO datatypes VALUES (9, 'Synthesis', 'file');
INSERT INTO datatypes VALUES (10, 'Document', 'file');
INSERT INTO datatypes VALUES (11, 'Structure ', 'file');
INSERT INTO datatypes VALUES (13, 'Image', 'file');
INSERT INTO datatypes VALUES (15, 'Other', 'file');
INSERT INTO datatypes VALUES (16, 'PyMOL Session', 'file');
INSERT INTO datatypes VALUES (17, 'Chimera Session', 'file');
INSERT INTO datatypes VALUES (18, 'Spreadsheet', 'file');
INSERT INTO datatypes VALUES (19, 'Docking Grid', 'file');

SELECT pg_catalog.setval('datatypes_datatypeid_seq', 19, true);


