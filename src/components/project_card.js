import React from "react";
import Card from "react-bootstrap/Card";
import CardDeck from "react-bootstrap/CardDeck";
import Button from "react-bootstrap/Button";
import Spinner from "react-bootstrap/Spinner";
import Accordion from "react-bootstrap/Accordion";
import "./project_card.css";

class Project_card extends React.Component {
  static defaultProps = {
    callback: text => alert(JSON.stringify(text))
  };
  handleclick(id) {
    this.props.callback(id);
  }
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      isLoaded: false,
      projects: {}
    };
  }
  componentDidMount() {
    fetch("//localhost/deepdaemon_web_controller/projects/"+ this.props.state, { method: "GET" })
      .then(res => res.json())
      .then(
        result => {
          this.setState({
            isLoaded: true,
            projects: result
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
    const { error, isLoaded, projects } = this.state;
    if (error) {
      return <div>Error: {error.message}</div>;
    } else if (!isLoaded) {
      return (
        <Button variant="primary" disabled>
          <Spinner
            as="span"
            animation="grow"
            size="sm"
            role="status"
            aria-hidden="true"
          />
          Loading...
        </Button>
      );
    } else {
      return (
        <CardDeck>
          {projects.map(project => (
              <Card key={project.id} className="project">
                <Card.Header>
                  <div>
                  {project.name}
                  </div>
                  <Button
                        variant="primary"
                        onClick={() => this.handleclick(project.id)}>
                        Ver más...
                      </Button> 
                </Card.Header>
              </Card>
          ))}
        </CardDeck>
      );
    }
  }
}

export default Project_card;
