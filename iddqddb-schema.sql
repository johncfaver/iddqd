--
-- PostgreSQL database dump
--

-- Dumped from database version 9.1.11
-- Dumped by pg_dump version 9.1.11
-- Started on 2014-01-14 14:38:45 EST

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 1994 (class 1262 OID 16385)
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
-- TOC entry 181 (class 3079 OID 11645)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 1997 (class 0 OID 0)
-- Dependencies: 181
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- TOC entry 182 (class 3079 OID 16386)
-- Dependencies: 6
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 1998 (class 0 OID 0)
-- Dependencies: 182
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 161 (class 1259 OID 16420)
-- Dependencies: 6
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
-- TOC entry 162 (class 1259 OID 16423)
-- Dependencies: 161 6
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
-- TOC entry 1999 (class 0 OID 0)
-- Dependencies: 162
-- Name: bindingdata_bindingdataid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE bindingdata_bindingdataid_seq OWNED BY moldata.moldataid;


--
-- TOC entry 163 (class 1259 OID 16425)
-- Dependencies: 1851 6
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
-- TOC entry 164 (class 1259 OID 16429)
-- Dependencies: 163 6
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
-- TOC entry 2000 (class 0 OID 0)
-- Dependencies: 164
-- Name: bounties_bountyid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE bounties_bountyid_seq OWNED BY bounties.bountyid;


--
-- TOC entry 165 (class 1259 OID 16431)
-- Dependencies: 6
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
-- TOC entry 166 (class 1259 OID 16437)
-- Dependencies: 165 6
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
-- TOC entry 2001 (class 0 OID 0)
-- Dependencies: 166
-- Name: bountycomments_bountycommentid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE bountycomments_bountycommentid_seq OWNED BY bountycomments.bountycommentid;


--
-- TOC entry 167 (class 1259 OID 16439)
-- Dependencies: 6
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
-- TOC entry 168 (class 1259 OID 16445)
-- Dependencies: 167 6
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
-- TOC entry 2002 (class 0 OID 0)
-- Dependencies: 168
-- Name: datacomment_datacommentid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE datacomment_datacommentid_seq OWNED BY datacomments.datacommentid;


--
-- TOC entry 169 (class 1259 OID 16447)
-- Dependencies: 6
-- Name: datatypes; Type: TABLE; Schema: public; Owner: iddqd; Tablespace: 
--

CREATE TABLE datatypes (
    datatypeid integer NOT NULL,
    type text NOT NULL,
    units text NOT NULL
);


ALTER TABLE public.datatypes OWNER TO iddqd;

--
-- TOC entry 170 (class 1259 OID 16453)
-- Dependencies: 6 169
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
-- TOC entry 2003 (class 0 OID 0)
-- Dependencies: 170
-- Name: datatypes_datatypeid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE datatypes_datatypeid_seq OWNED BY datatypes.datatypeid;

--
-- TOC entry 171 (class 1259 OID 16455)
-- Dependencies: 6
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
-- TOC entry 172 (class 1259 OID 16461)
-- Dependencies: 6 171
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
-- TOC entry 2004 (class 0 OID 0)
-- Dependencies: 172
-- Name: molcomments_molcommentid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE molcomments_molcommentid_seq OWNED BY molcomments.molcommentid;


--
-- TOC entry 173 (class 1259 OID 16463)
-- Dependencies: 6
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
-- TOC entry 174 (class 1259 OID 16469)
-- Dependencies: 173 6
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
-- TOC entry 2005 (class 0 OID 0)
-- Dependencies: 174
-- Name: molecules_molid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE molecules_molid_seq OWNED BY molecules.molid;


--
-- TOC entry 179 (class 1259 OID 18952)
-- Dependencies: 1860 6
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
-- TOC entry 175 (class 1259 OID 16471)
-- Dependencies: 6
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
-- TOC entry 176 (class 1259 OID 16477)
-- Dependencies: 175 6
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
-- TOC entry 2006 (class 0 OID 0)
-- Dependencies: 176
-- Name: targets_targetid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE targets_targetid_seq OWNED BY targets.targetid;


--
-- TOC entry 180 (class 1259 OID 18961)
-- Dependencies: 6
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
-- TOC entry 177 (class 1259 OID 16479)
-- Dependencies: 6
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
-- TOC entry 178 (class 1259 OID 16485)
-- Dependencies: 177 6
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
-- TOC entry 2007 (class 0 OID 0)
-- Dependencies: 178
-- Name: users_userid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE users_userid_seq OWNED BY users.userid;


--
-- TOC entry 1852 (class 2604 OID 16487)
-- Dependencies: 164 163
-- Name: bountyid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY bounties ALTER COLUMN bountyid SET DEFAULT nextval('bounties_bountyid_seq'::regclass);


--
-- TOC entry 1853 (class 2604 OID 16488)
-- Dependencies: 166 165
-- Name: bountycommentid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY bountycomments ALTER COLUMN bountycommentid SET DEFAULT nextval('bountycomments_bountycommentid_seq'::regclass);


--
-- TOC entry 1854 (class 2604 OID 16489)
-- Dependencies: 168 167
-- Name: datacommentid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY datacomments ALTER COLUMN datacommentid SET DEFAULT nextval('datacomment_datacommentid_seq'::regclass);


--
-- TOC entry 1855 (class 2604 OID 16490)
-- Dependencies: 170 169
-- Name: datatypeid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY datatypes ALTER COLUMN datatypeid SET DEFAULT nextval('datatypes_datatypeid_seq'::regclass);


--
-- TOC entry 1856 (class 2604 OID 16491)
-- Dependencies: 172 171
-- Name: molcommentid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY molcomments ALTER COLUMN molcommentid SET DEFAULT nextval('molcomments_molcommentid_seq'::regclass);


--
-- TOC entry 1850 (class 2604 OID 16492)
-- Dependencies: 162 161
-- Name: moldataid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY moldata ALTER COLUMN moldataid SET DEFAULT nextval('bindingdata_bindingdataid_seq'::regclass);


--
-- TOC entry 1857 (class 2604 OID 16493)
-- Dependencies: 174 173
-- Name: molid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY molecules ALTER COLUMN molid SET DEFAULT nextval('molecules_molid_seq'::regclass);


--
-- TOC entry 1858 (class 2604 OID 16494)
-- Dependencies: 176 175
-- Name: targetid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY targets ALTER COLUMN targetid SET DEFAULT nextval('targets_targetid_seq'::regclass);


--
-- TOC entry 1859 (class 2604 OID 16495)
-- Dependencies: 178 177
-- Name: userid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY users ALTER COLUMN userid SET DEFAULT nextval('users_userid_seq'::regclass);


--
-- TOC entry 1862 (class 2606 OID 16497)
-- Dependencies: 161 161 1991
-- Name: bindingdatakey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY moldata
    ADD CONSTRAINT bindingdatakey PRIMARY KEY (moldataid);


--
-- TOC entry 1864 (class 2606 OID 16499)
-- Dependencies: 161 161 1991
-- Name: bindingdataunique; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY moldata
    ADD CONSTRAINT bindingdataunique UNIQUE (moldataid);


--
-- TOC entry 1866 (class 2606 OID 16501)
-- Dependencies: 163 163 1991
-- Name: bounties_pkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY bounties
    ADD CONSTRAINT bounties_pkey PRIMARY KEY (bountyid);


--
-- TOC entry 1868 (class 2606 OID 16503)
-- Dependencies: 165 165 1991
-- Name: bountycomments_pkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY bountycomments
    ADD CONSTRAINT bountycomments_pkey PRIMARY KEY (bountycommentid);


--
-- TOC entry 1886 (class 2606 OID 18960)
-- Dependencies: 179 179 1991
-- Name: changkeyindex; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY passwordchanges
    ADD CONSTRAINT changkeyindex PRIMARY KEY (changekey);


--
-- TOC entry 1870 (class 2606 OID 16505)
-- Dependencies: 167 167 1991
-- Name: datacomentkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY datacomments
    ADD CONSTRAINT datacomentkey PRIMARY KEY (datacommentid);


--
-- TOC entry 1872 (class 2606 OID 16507)
-- Dependencies: 169 169 1991
-- Name: datatypeskey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY datatypes
    ADD CONSTRAINT datatypeskey PRIMARY KEY (datatypeid);


--
-- TOC entry 1874 (class 2606 OID 16509)
-- Dependencies: 171 171 1991
-- Name: molcommentkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY molcomments
    ADD CONSTRAINT molcommentkey PRIMARY KEY (molcommentid);


--
-- TOC entry 1876 (class 2606 OID 16511)
-- Dependencies: 173 173 1991
-- Name: molidkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY molecules
    ADD CONSTRAINT molidkey PRIMARY KEY (molid);


--
-- TOC entry 1878 (class 2606 OID 16513)
-- Dependencies: 173 173 1991
-- Name: molidunique; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY molecules
    ADD CONSTRAINT molidunique UNIQUE (molid);


--
-- TOC entry 1880 (class 2606 OID 16515)
-- Dependencies: 175 175 1991
-- Name: targetidkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targetidkey PRIMARY KEY (targetid);


--
-- TOC entry 1882 (class 2606 OID 16517)
-- Dependencies: 175 175 1991
-- Name: targetidunique; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targetidunique UNIQUE (targetid);


--
-- TOC entry 1888 (class 2606 OID 18968)
-- Dependencies: 180 180 1991
-- Name: tokenskey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY tokens
    ADD CONSTRAINT tokenskey PRIMARY KEY (userid);


--
-- TOC entry 1884 (class 2606 OID 16519)
-- Dependencies: 177 177 1991
-- Name: useridkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT useridkey PRIMARY KEY (userid);


--
-- TOC entry 1996 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2014-01-14 14:38:45 EST

--
-- PostgreSQL database dump complete
--



-- Default values for IDDQD
INSERT INTO datatypes (type, units) VALUES ('ic50', 'µM');
INSERT INTO datatypes (type, units) VALUES ('ec50', 'µM');
INSERT INTO datatypes (type, units) VALUES ('kd', 'µM');
INSERT INTO datatypes (type, units) VALUES ('cc50', 'µM');
INSERT INTO datatypes (type, units) VALUES ('solwater', 'g/L');
INSERT INTO datatypes (type, units) VALUES ('H NMR', 'file');
INSERT INTO datatypes (type, units) VALUES ('C NMR', 'file');
INSERT INTO datatypes (type, units) VALUES ('Mass Spec.', 'file');
INSERT INTO datatypes (type, units) VALUES ('Synthesis', 'file');
INSERT INTO datatypes (type, units) VALUES ('Manuscript', 'file');
INSERT INTO datatypes (type, units) VALUES ('Structure ', 'file');
INSERT INTO datatypes (type, units) VALUES ('Image', 'file');
INSERT INTO datatypes (type, units) VALUES ('Other', 'file');


