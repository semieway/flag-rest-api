--
-- PostgreSQL database dump
--

-- Dumped from database version 13.2 (Ubuntu 13.2-1.pgdg20.04+1)
-- Dumped by pg_dump version 13.2 (Ubuntu 13.2-1.pgdg20.04+1)

-- Started on 2021-03-05 16:31:09 +05

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 205 (class 1259 OID 1683145)
-- Name: actors; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.actors (
    id integer NOT NULL,
    first_name character varying NOT NULL,
    last_name character varying,
    birth_date date
);


--
-- TOC entry 204 (class 1259 OID 1683143)
-- Name: actors_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.actors_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 4011 (class 0 OID 0)
-- Dependencies: 204
-- Name: actors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.actors_id_seq OWNED BY public.actors.id;


--
-- TOC entry 201 (class 1259 OID 1681991)
-- Name: genres; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.genres (
    id integer NOT NULL,
    name character varying NOT NULL
);


--
-- TOC entry 200 (class 1259 OID 1681989)
-- Name: genre_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.genre_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 4012 (class 0 OID 0)
-- Dependencies: 200
-- Name: genre_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.genre_id_seq OWNED BY public.genres.id;


--
-- TOC entry 206 (class 1259 OID 1689325)
-- Name: movie_actors; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.movie_actors (
    movie_id integer NOT NULL,
    actor_id integer NOT NULL
);


--
-- TOC entry 203 (class 1259 OID 1682235)
-- Name: movies; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.movies (
    id integer NOT NULL,
    title character varying NOT NULL,
    year integer NOT NULL,
    genre_id integer,
    overview character varying,
    runtime integer
);


--
-- TOC entry 202 (class 1259 OID 1682233)
-- Name: movies_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.movies_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 4013 (class 0 OID 0)
-- Dependencies: 202
-- Name: movies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.movies_id_seq OWNED BY public.movies.id;


--
-- TOC entry 3858 (class 2604 OID 1683148)
-- Name: actors id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.actors ALTER COLUMN id SET DEFAULT nextval('public.actors_id_seq'::regclass);


--
-- TOC entry 3856 (class 2604 OID 1681994)
-- Name: genres id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.genres ALTER COLUMN id SET DEFAULT nextval('public.genre_id_seq'::regclass);


--
-- TOC entry 3857 (class 2604 OID 1682238)
-- Name: movies id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movies ALTER COLUMN id SET DEFAULT nextval('public.movies_id_seq'::regclass);


--
-- TOC entry 4004 (class 0 OID 1683145)
-- Dependencies: 205
-- Data for Name: actors; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.actors (id, first_name, last_name, birth_date) FROM stdin;
1	Marlon	Brando	1924-04-03
2	Al	Pacino	1940-04-25
3	Tim	Robbins	1958-10-16
4	Morgan	Freeman	1937-06-01
5	Martin	Balsam	1919-11-04
6	John	Fiedler	1925-02-03
7	John	Travolta	1954-02-18
8	Samuel	L. Jackson	1948-12-21
9	Tim	Roth	1961-05-14
10	Roberto	Benigni	1957-10-27
11	Nicoletta	Braschi	1960-04-19
12	Arnold	Schwarzenegger	1947-07-30
13	Linda	Hamilton	1956-09-26
\.


--
-- TOC entry 4000 (class 0 OID 1681991)
-- Dependencies: 201
-- Data for Name: genres; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.genres (id, name) FROM stdin;
1	action
2	drama
3	horror
4	comedy
5	crime
6	thriller
7	documentary
\.


--
-- TOC entry 4005 (class 0 OID 1689325)
-- Dependencies: 206
-- Data for Name: movie_actors; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.movie_actors (movie_id, actor_id) FROM stdin;
1	1
1	2
2	3
2	4
3	5
3	6
4	7
4	8
4	9
5	10
5	11
6	12
6	13
\.


--
-- TOC entry 4002 (class 0 OID 1682235)
-- Dependencies: 203
-- Data for Name: movies; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.movies (id, title, year, genre_id, overview, runtime) FROM stdin;
1	The Godfather	1972	5	An organized crime dynasty's aging patriarch transfers control of his clandestine empire to his reluctant son.	175
2	The Shawshank Redemption	1994	2	Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.	142
3	12 Angry Men	1957	5	A jury holdout attempts to prevent a miscarriage of justice by forcing his colleagues to reconsider the evidence.	96
4	Pulp Fiction	1994	5	The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.	154
5	Life Is Beautiful	1997	4	When an open-minded Jewish librarian and his son become victims of the Holocaust, he uses a perfect mixture of will, humor, and imagination to protect his son from the dangers around their camp.	116
6	Terminator 2: Judgment Day	1991	1	A cyborg, identical to the one who failed to kill Sarah Connor, must now protect her ten year old son, John Connor, from a more advanced and powerful cyborg.	137
\.


--
-- TOC entry 4014 (class 0 OID 0)
-- Dependencies: 204
-- Name: actors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.actors_id_seq', 13, true);


--
-- TOC entry 4015 (class 0 OID 0)
-- Dependencies: 200
-- Name: genre_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.genre_id_seq', 7, true);


--
-- TOC entry 4016 (class 0 OID 0)
-- Dependencies: 202
-- Name: movies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.movies_id_seq', 6, true);


--
-- TOC entry 3865 (class 2606 OID 1683153)
-- Name: actors actors_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.actors
    ADD CONSTRAINT actors_pkey PRIMARY KEY (id);


--
-- TOC entry 3860 (class 2606 OID 1681999)
-- Name: genres genre_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.genres
    ADD CONSTRAINT genre_pkey PRIMARY KEY (id);


--
-- TOC entry 3862 (class 2606 OID 1682243)
-- Name: movies movies_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movies
    ADD CONSTRAINT movies_pkey PRIMARY KEY (id);


--
-- TOC entry 3867 (class 2606 OID 1689329)
-- Name: movie_actors primary_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movie_actors
    ADD CONSTRAINT primary_key PRIMARY KEY (movie_id, actor_id);


--
-- TOC entry 3863 (class 1259 OID 1926811)
-- Name: title; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX title ON public.movies USING btree (title varchar_ops);


--
-- TOC entry 3868 (class 2606 OID 1682246)
-- Name: movies genre_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movies
    ADD CONSTRAINT genre_id FOREIGN KEY (genre_id) REFERENCES public.genres(id) NOT VALID;


-- Completed on 2021-03-05 16:31:23 +05

--
-- PostgreSQL database dump complete
--

