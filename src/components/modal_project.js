import React from "react";
import {
  Modal,
  Image,
  Spinner,
  Button,
  Container,
  OverlayTrigger,
  Tooltip
} from "react-bootstrap";
import System from "./System.js";
import "./modal_project.css";

class Modal_project extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      isLoaded: false,
      showModal: false
    };
  }

  close() {
    this.setState({ showModal: false, isLoaded: false, error: null });
  }

  open(id) {
    this.setState({ showModal: true });
    fetch(System.urlConection + System.projects + id, {
      method: "GET"
    })
      .then(res => res.json())
      .then(
        result => {
          this.setState({
            isLoaded: true,
            project: result
          });
        },
        error => {
          this.setState({
            isLoaded: true,
            error
          });
        }
      );
  }

  render() {
    const { error, isLoaded, project } = this.state;
    if (!isLoaded) {
      return (
        <Modal
          centered
          size="lg"
          className="DeepModal loading"
          show={this.state.showModal}
          onHide={this.close.bind(this)}>
          <Modal.Header closeButton>
            <Modal.Title>
              <Spinner
                as="span"
                animation="grow"
                role="status"
                aria-hidden="true"
              />
              Loading...
            </Modal.Title>
          </Modal.Header>
          <Modal.Footer>
            <Spinner
              as="span"
              animation="grow"
              role="status"
              aria-hidden="true"
            />
            Loading...
          </Modal.Footer>
        </Modal>
      );
    }
    if (error) {
      return (
        <Modal
          size="lg"
          centered
          className="DeepModal"
          show={this.state.showModal}
          onHide={this.close.bind(this)}>
          <Button variant="danger" disabled>
            Error: {error.message}
          </Button>
        </Modal>
      );
    } else {
      let media;
      switch (project.modal_type) {
        case "image":
        default:
          media = (
            <Image
              className="media"
              src={
                project.modal_media != null
                  ? `${process.env.PUBLIC_URL}/media/projects/${
                      project.modal_media
                    }`
                  : require("../assets/img/project_detail.jpg")
              }
              alt={
                project.modal_media != null
                  ? `${process.env.PUBLIC_URL}/media/projects/${
                      project.modal_media
                    }`
                  : require("../assets/img/project_detail.jpg")
              }
            />
          );
          break;
        case "video":
          media = (
            <video autoPlay loop className="media">
              <source
                src={
                  project.modal_media != null
                    ? `${process.env.PUBLIC_URL}/media/projects/${
                        project.modal_media
                      }`
                    : require("../assets/img/project_media.mp4")
                }
                type="video/mp4"
              />
              Your browser does not support the video tag.
            </video>
          );
          break;
      }

      return (
        <Modal
          centered
          size="lg"
          className="DeepProjectModal"
          show={this.state.showModal}
          onHide={this.close.bind(this)}>
          <Modal.Header closeButton>
            <Modal.Title>{project.name}</Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <Container>{project.desc}</Container>
            <Container>
              {media}
              {project.impact}
            </Container>
            <Container fluid>
              <h3>Tecnologías</h3>
              {project.tech_short.map((tc, key) => {
                return (
                  <OverlayTrigger
                    key={key}
                    placement="auto"
                    overlay={<Tooltip>{project.tech_long[key]}</Tooltip>}>
                    <span
                      className="tech"
                      style={{
                        backgroundColor: `rgb(${Math.floor(
                          Math.random() * 256
                        )},${Math.floor(Math.random() * 256)},${Math.floor(
                          Math.random() * 256
                        )})`
                      }}>
                      {tc}
                    </span>
                  </OverlayTrigger>
                );
              })}
            </Container>
          </Modal.Body>
          <Modal.Footer>
            <Container>
              <h3>Autores</h3>
              {project.members.map((member, key) => {
                return <div key={key}>{member}</div>;
              })}
            </Container>
            <Button variant="secondary" href={project.link} target="_blank">
              Conocer más...
            </Button>
          </Modal.Footer>
        </Modal>
      );
    }
  }
}

export default Modal_project;
