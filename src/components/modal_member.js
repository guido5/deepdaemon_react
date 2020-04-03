import React from "react";
import { Modal, Image, Spinner, Button, Container } from "react-bootstrap";
import System from "./System.js";
import "./modal_member.css";

class Modal_member extends React.Component {
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
    fetch(System.urlConection + System.members + id, {
      method: "GET", cache: "no-store"
    })
      .then(res => res.json())
      .then(
        result => {
          this.setState({
            isLoaded: true,
            member: result
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
    const { error, isLoaded, member } = this.state;
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
          <Modal.Body>
            <Image
              roundedCircle
              src={require("../assets/img/user.png")}
              alt="user.png"
            />
            <Container className="member_content">
              <Container>
                <Spinner
                  as="span"
                  animation="grow"
                  role="status"
                  aria-hidden="true"
                />
                Loading...
              </Container>
              <Container>
                <Spinner
                  as="span"
                  animation="grow"
                  role="status"
                  aria-hidden="true"
                />
                Loading...
              </Container>
            </Container>
          </Modal.Body>
          <Modal.Footer>
            Loading...
            <Spinner
              as="span"
              animation="grow"
              role="status"
              aria-hidden="true"
              size="sm"
            />
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
      const filename =
        member.photo_filename != null
          ? `${process.env.PUBLIC_URL}/media/team/${
              member.photo_filename
            }`
          : require("../assets/img/user.png");
      return (
        <Modal
          centered
          size="lg"
          className="DeepModal"
          show={this.state.showModal}
          onHide={this.close.bind(this)}>
          <Modal.Header closeButton>
            <Modal.Title>{member.name}</Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <Image roundedCircle src={filename} alt={filename} />
            <Container className="member_content">
              <Container>{member.long_desc}</Container>
              <Container>
                <b>Proyectos en los que ha participado:</b>
                <ul>
                  {member.projects.map((pj, key) => {
                    return <li key={key}>{pj}</li>;
                  })}
                </ul>
              </Container>
            </Container>
          </Modal.Body>
          <Modal.Footer>
            <span className="fas fa-envelope" />
            <span className="fab fa-linkedin" />
            <span className="fab fa-facebook" />
            <span className="fab fa-twitter" />
            <span className="fab fa-github" />
            <Spinner animation="grow" size="sm" />
          </Modal.Footer>
        </Modal>
      );
    }
  }
}

export default Modal_member;
