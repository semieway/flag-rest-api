--
-- PostgreSQL database dump
--

-- Dumped from database version 13.2 (Ubuntu 13.2-1.pgdg20.04+1)
-- Dumped by pg_dump version 13.2 (Ubuntu 13.2-1.pgdg20.04+1)

-- Started on 2021-03-07 14:21:15 +05

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
-- TOC entry 4006 (class 0 OID 0)
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
-- TOC entry 4007 (class 0 OID 0)
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
-- TOC entry 4008 (class 0 OID 0)
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
-- TOC entry 3870 (class 2606 OID 2104347)
-- Name: movie_actors actor_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movie_actors
    ADD CONSTRAINT actor_id FOREIGN KEY (actor_id) REFERENCES public.actors(id) NOT VALID;


--
-- TOC entry 3868 (class 2606 OID 1682246)
-- Name: movies genre_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movies
    ADD CONSTRAINT genre_id FOREIGN KEY (genre_id) REFERENCES public.genres(id) NOT VALID;


--
-- TOC entry 3869 (class 2606 OID 2104342)
-- Name: movie_actors movie_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movie_actors
    ADD CONSTRAINT movie_id FOREIGN KEY (movie_id) REFERENCES public.movies(id) NOT VALID;


-- Completed on 2021-03-07 14:21:29 +05

--
-- PostgreSQL database dump complete
--

