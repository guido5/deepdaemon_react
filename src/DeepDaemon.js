import React from "react";
import Container from "react-bootstrap/Container";
import Col from "react-bootstrap/Col";
import Row from "react-bootstrap/Row";
import Image from "react-bootstrap/Image";
import Button from "react-bootstrap/Button";
import Navbar from "react-bootstrap/Navbar";
import Nav from "react-bootstrap/Nav";

import logo from "./assets/img/deepdaemon.png";
import join from "./assets/img/kissclipart-logo-intelligence-clipart-machine-learning-artific-0eb8b24961c90301.png";
import "./assets/icomoon/icomoon.css"; //https://icomoon.io/#preview-free

import "./DeepDaemon.css";
import Projects from "./components/project_card";
import Team from "./components/team_card";
import TeamTabs from "./components/team_tabs";
import ModalMember from "./components/modal_member";
import ModalProject from "./components/modal_project";
import {Tab } from "react-bootstrap";

class DeepDaemon extends React.Component {
  constructor() {
    super();
    this.membermodal = React.createRef();
    this.projectmodal = React.createRef();
    this.home = React.createRef();
    this.about = React.createRef();
    this.portfolio = React.createRef();
    this.leaders = React.createRef();
    this.team = React.createRef();
    this.contact = React.createRef();
  }

  scroll(ref) {
    ref.current.scrollIntoView({ behavior: "smooth" });
  }

  updateModal(id) {
    this.membermodal.open(id);
  }

  openProject(id) {
    this.projectmodal.open(id);
  }

  render() {
    return (
      <div className="DeepDaemon" ref={this.home}>
        <Navbar bg="dark" variant="dark" expand="lg" sticky="top">
          <Navbar.Brand>
            <img src={logo} style={{ width: "30px" }} alt="logo" />
            DeepDaemon
          </Navbar.Brand>
          <Navbar.Toggle aria-controls="basic-navbar-nav" />
          <Navbar.Collapse>
            <Nav className="mr-auto">
              <Nav.Link
                onClick={() => {
                  this.scroll(this.home);
                }}>
                Home
              </Nav.Link>
              <Nav.Link
                onClick={() => {
                  this.scroll(this.about);
                }}>
                Nosotros
              </Nav.Link>
              <Nav.Link
                onClick={() => {
                  this.scroll(this.portfolio);
                }}>
                Proyectos
              </Nav.Link>
              <Nav.Link
                onClick={() => {
                  this.scroll(this.leaders);
                }}>
                Líderes
              </Nav.Link>
              <Nav.Link
                onClick={() => {
                  this.scroll(this.team);
                }}>
                Equipo
              </Nav.Link>
              <Nav.Link
                onClick={() => {
                  this.scroll(this.contact);
                }}>
                Contacto
              </Nav.Link>
            </Nav>
          </Navbar.Collapse>
        </Navbar>
        <header className="dd_header">
          <img src={logo} className="ddlogo" alt="logo" />
          <h1>DeepDaemon</h1>
          <hr />
          <h2>Comunidad de conocimiento</h2>
        </header>
        <Container className="section" ref={this.about}>
          <h1>Un poco sobre nosotros</h1>
          <hr />
          <p>
            Somos un grupo de trabajo que busca vincular el desarrollo
            científico con el desarrollo de soluciones industriales para generar
            tecnología de punta y capital humano de alto impacto en el ámbito
            académico e industrial.
          </p>
        </Container>
        <Container className="section">
          <Row>
            <Col>
              <span className="icon icon-eye " />
              <h2>Visión</h2>
              <p>
                Que el grupo de trabajo y sus integrantes sean un referente a
                nivel mundial en el desarrollo de tecnologías de punta a nivel
                científico, académico y comercial, capacitando a capital humano
                de excelente calidad y desarrollando proyectos con alto impacto
                comercial y social.
              </p>
            </Col>
            <Col>
              <span className="icon icon-quill" />
              <h2>Misión</h2>
              <p>
                Desarrollar sistemas inteligentes basados en redes neuronales
                profundas que puedan ser distribuidos a usuarios reales, con el
                objetivo de favorecer una educación integral a los estudiantes
                del grupo de trabajo.
              </p>
            </Col>
            <Col>
              <span className="icon icon-heart" />
              <h2>Valores</h2>
              <p>
                Intregridad, Confianza, Comunicaciones honestas y abiertas,
                Pasión por trabajar para hacer un cambio en el mundo.
              </p>
            </Col>
          </Row>
        </Container>
        <Container fluid className="academy">
          <Container className="section white">
            <h1>La Investigación no solo es académica</h1>
            <hr />
            <p>
              Por eso creamos un grupo de investigación que tiene como objetivo
              usar la inteligencia artificial para resolver problemas de la
              industria.
            </p>
          </Container>
        </Container>
        <Container fluid className="section portfolio" ref={this.portfolio}>
        <h1>Proyectos increibles</h1>

          <Tab.Container defaultActiveKey="inDevelop">
            <Nav className="projects">
              <Nav.Item><Nav.Link eventKey="inDevelop"><h1>En desarrollo</h1></Nav.Link></Nav.Item>
              <Nav.Item><Nav.Link eventKey="article"><h1>Concluidos</h1></Nav.Link></Nav.Item>
            </Nav>
            <hr />
            <Tab.Content>
                <Tab.Pane eventKey="inDevelop"><Projects state="inDevelop" callback={this.openProject.bind(this)} /></Tab.Pane>
                <Tab.Pane eventKey="article"><Projects state="finish" callback={this.openProject.bind(this)} /></Tab.Pane>
            </Tab.Content>
          </Tab.Container>
          
        </Container>
        <Container className="section">
          <Row>
            <Col md="auto">
              <Image src={join} style={{ height: "320px" }} fluid />
            </Col>
            <Col>
              <h1>Únete al equipo!</h1>
              <p>
                Si eres estudiante de Licenciatura o Posgrado en el IPN, o estás
                interesdo en complementar tu educación con un postgrado en el
                Centro de Investigación en Computación, y quieres desarrollar
                proyectos de IA aplicados a problemas reales o industriales, te
                estamos buscando.
              </p>
              <Button variant="secondary">Contáctanos</Button>
            </Col>
          </Row>
        </Container>
        <Container fluid className="team_separator">
          <Container className="section white">
            <h1>Inteligencia colectiva</h1>
            <hr />
            <h2>
              Alumnos e Investigadores colaborando para resolver problemas
              multidisciplinarios.
            </h2>
          </Container>
        </Container>
        <Container fluid className="section" ref={this.leaders}>
          <h1>Los líderes</h1>
          <hr />
          <Team status="leader" callback={this.updateModal.bind(this)} />
        </Container>
        <Container fluid className="section" ref={this.team}>
          <TeamTabs callback={this.updateModal.bind(this)} />
        </Container>
        <Container fluid className="section contact white" ref={this.contact}>
          <h1>Hecho en la Ciudad de México</h1>
          <hr />
          <p>
            <span className="icon icon-home" />
            Av. Juan de Dios Bátiz, Esq. Miguel Othón de Mendizábal,
            <br />
            Col. Nueva Industrial Vallejo, Delegación Gustavo A. Madero. CDMX
          </p>
          <p>
            <span className="icon icon-envelop" />
            <a href="mailto:contacto@deepdaemon.org">
              contacto@deepdaemon.org{" "}
            </a>
          </p>
          <p>
            <a href="https://twitter.com">
              <span className="icon icon-twitter" />
            </a>
            |
            <a href="https://www.facebook.com">
              <span className="icon icon-facebook" />
            </a>
          </p>
          <hr />
          <footer>&copy; DeepDaemon 2019</footer>
        </Container>
        <ModalMember
          ref={ip => {
            this.membermodal = ip;            
          }}
        />
        <ModalProject
          ref={ip => {
            this.projectmodal = ip;
          }}
        />
      </div>
    );
  }
}

export default DeepDaemon;
