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

--
-- TOC entry 2038 (class 1262 OID 16385)
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
-- TOC entry 182 (class 3079 OID 11681)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2041 (class 0 OID 0)
-- Dependencies: 182
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- TOC entry 183 (class 3079 OID 16386)
-- Dependencies: 6
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 2042 (class 0 OID 0)
-- Dependencies: 183
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
-- TOC entry 2043 (class 0 OID 0)
-- Dependencies: 162
-- Name: bindingdata_bindingdataid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE bindingdata_bindingdataid_seq OWNED BY moldata.moldataid;


--
-- TOC entry 163 (class 1259 OID 16425)
-- Dependencies: 1892 6
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
-- TOC entry 2044 (class 0 OID 0)
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
-- TOC entry 2045 (class 0 OID 0)
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
-- TOC entry 2046 (class 0 OID 0)
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
-- TOC entry 2047 (class 0 OID 0)
-- Dependencies: 170
-- Name: datatypes_datatypeid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE datatypes_datatypeid_seq OWNED BY datatypes.datatypeid;


--
-- TOC entry 181 (class 1259 OID 16546)
-- Dependencies: 6
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
-- Dependencies: 171 6
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
-- TOC entry 2048 (class 0 OID 0)
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
-- TOC entry 2049 (class 0 OID 0)
-- Dependencies: 174
-- Name: molecules_molid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE molecules_molid_seq OWNED BY molecules.molid;


--
-- TOC entry 175 (class 1259 OID 16471)
-- Dependencies: 1899 6
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
-- TOC entry 176 (class 1259 OID 16478)
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
-- TOC entry 177 (class 1259 OID 16484)
-- Dependencies: 6 176
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
-- TOC entry 2050 (class 0 OID 0)
-- Dependencies: 177
-- Name: targets_targetid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE targets_targetid_seq OWNED BY targets.targetid;


--
-- TOC entry 178 (class 1259 OID 16486)
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
-- TOC entry 179 (class 1259 OID 16492)
-- Dependencies: 1902 6
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
-- TOC entry 180 (class 1259 OID 16498)
-- Dependencies: 6 179
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
-- TOC entry 2051 (class 0 OID 0)
-- Dependencies: 180
-- Name: users_userid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: iddqd
--

ALTER SEQUENCE users_userid_seq OWNED BY users.userid;


--
-- TOC entry 1893 (class 2604 OID 16500)
-- Dependencies: 164 163
-- Name: bountyid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY bounties ALTER COLUMN bountyid SET DEFAULT nextval('bounties_bountyid_seq'::regclass);


--
-- TOC entry 1894 (class 2604 OID 16501)
-- Dependencies: 166 165
-- Name: bountycommentid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY bountycomments ALTER COLUMN bountycommentid SET DEFAULT nextval('bountycomments_bountycommentid_seq'::regclass);


--
-- TOC entry 1895 (class 2604 OID 16502)
-- Dependencies: 168 167
-- Name: datacommentid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY datacomments ALTER COLUMN datacommentid SET DEFAULT nextval('datacomment_datacommentid_seq'::regclass);


--
-- TOC entry 1896 (class 2604 OID 16503)
-- Dependencies: 170 169
-- Name: datatypeid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY datatypes ALTER COLUMN datatypeid SET DEFAULT nextval('datatypes_datatypeid_seq'::regclass);


--
-- TOC entry 1897 (class 2604 OID 16504)
-- Dependencies: 172 171
-- Name: molcommentid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY molcomments ALTER COLUMN molcommentid SET DEFAULT nextval('molcomments_molcommentid_seq'::regclass);


--
-- TOC entry 1891 (class 2604 OID 16505)
-- Dependencies: 162 161
-- Name: moldataid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY moldata ALTER COLUMN moldataid SET DEFAULT nextval('bindingdata_bindingdataid_seq'::regclass);


--
-- TOC entry 1898 (class 2604 OID 16506)
-- Dependencies: 174 173
-- Name: molid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY molecules ALTER COLUMN molid SET DEFAULT nextval('molecules_molid_seq'::regclass);


--
-- TOC entry 1900 (class 2604 OID 16507)
-- Dependencies: 177 176
-- Name: targetid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY targets ALTER COLUMN targetid SET DEFAULT nextval('targets_targetid_seq'::regclass);


--
-- TOC entry 1901 (class 2604 OID 16508)
-- Dependencies: 180 179
-- Name: userid; Type: DEFAULT; Schema: public; Owner: iddqd
--

ALTER TABLE ONLY users ALTER COLUMN userid SET DEFAULT nextval('users_userid_seq'::regclass);


--
-- TOC entry 1904 (class 2606 OID 16510)
-- Dependencies: 161 161 2035
-- Name: bindingdatakey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY moldata
    ADD CONSTRAINT bindingdatakey PRIMARY KEY (moldataid);


--
-- TOC entry 1906 (class 2606 OID 16512)
-- Dependencies: 161 161 2035
-- Name: bindingdataunique; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY moldata
    ADD CONSTRAINT bindingdataunique UNIQUE (moldataid);


--
-- TOC entry 1908 (class 2606 OID 16514)
-- Dependencies: 163 163 2035
-- Name: bounties_pkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY bounties
    ADD CONSTRAINT bounties_pkey PRIMARY KEY (bountyid);


--
-- TOC entry 1910 (class 2606 OID 16516)
-- Dependencies: 165 165 2035
-- Name: bountycomments_pkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY bountycomments
    ADD CONSTRAINT bountycomments_pkey PRIMARY KEY (bountycommentid);


--
-- TOC entry 1922 (class 2606 OID 16518)
-- Dependencies: 175 175 2035
-- Name: changkeyindex; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY passwordchanges
    ADD CONSTRAINT changkeyindex PRIMARY KEY (changekey);


--
-- TOC entry 1912 (class 2606 OID 16520)
-- Dependencies: 167 167 2035
-- Name: datacomentkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY datacomments
    ADD CONSTRAINT datacomentkey PRIMARY KEY (datacommentid);


--
-- TOC entry 1914 (class 2606 OID 16522)
-- Dependencies: 169 169 2035
-- Name: datatypeskey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY datatypes
    ADD CONSTRAINT datatypeskey PRIMARY KEY (datatypeid);


--
-- TOC entry 1932 (class 2606 OID 16555)
-- Dependencies: 181 181 2035
-- Name: invitesprimarykey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY invites
    ADD CONSTRAINT invitesprimarykey PRIMARY KEY (invitekey);


--
-- TOC entry 1916 (class 2606 OID 16524)
-- Dependencies: 171 171 2035
-- Name: molcommentkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY molcomments
    ADD CONSTRAINT molcommentkey PRIMARY KEY (molcommentid);


--
-- TOC entry 1918 (class 2606 OID 16526)
-- Dependencies: 173 173 2035
-- Name: molidkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY molecules
    ADD CONSTRAINT molidkey PRIMARY KEY (molid);


--
-- TOC entry 1920 (class 2606 OID 16528)
-- Dependencies: 173 173 2035
-- Name: molidunique; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY molecules
    ADD CONSTRAINT molidunique UNIQUE (molid);


--
-- TOC entry 1924 (class 2606 OID 16530)
-- Dependencies: 176 176 2035
-- Name: targetidkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targetidkey PRIMARY KEY (targetid);


--
-- TOC entry 1926 (class 2606 OID 16532)
-- Dependencies: 176 176 2035
-- Name: targetidunique; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targetidunique UNIQUE (targetid);


--
-- TOC entry 1928 (class 2606 OID 16534)
-- Dependencies: 178 178 2035
-- Name: tokenskey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY tokens
    ADD CONSTRAINT tokenskey PRIMARY KEY (userid);


--
-- TOC entry 1930 (class 2606 OID 16536)
-- Dependencies: 179 179 2035
-- Name: useridkey; Type: CONSTRAINT; Schema: public; Owner: iddqd; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT useridkey PRIMARY KEY (userid);


--
-- TOC entry 2040 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

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


