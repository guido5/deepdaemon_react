import React from "react";
import Card from "react-bootstrap/Card";
import CardDeck from "react-bootstrap/CardDeck";
import Image from "react-bootstrap/Image";
import OverlayTrigger from "react-bootstrap/OverlayTrigger";
import Tooltip from "react-bootstrap/Tooltip";
import Spinner from "react-bootstrap/Spinner";
import Button from "react-bootstrap/Button";
import Popover from "react-bootstrap/Popover";
import System from "./System.js";
import "./team_card.css";

class Team_card extends React.Component {
  static defaultProps = {
    status: "current",
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
      team: {}
    };
  }
  componentDidMount() {
    ////api.deepdaemon.org/members/
    fetch(System.urlConection + System.members + this.props.status, {
      method: "GET"
    })
      .then(res => res.json())
      .then(
        result => {
          this.setState({
            isLoaded: true,
            team: result
          });
        },
        // Note: it's important to handle errors here
        // instead of a catch() block so that we don't swallow
        // exceptions from actual bugs in components.
        error => {
          this.setState({
            isLoaded: true,
            error
          });
        }
      );
  }

  render() {
    const { error, isLoaded, team } = this.state;
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
          {team.map(person => {
            const filename =
              person.photo_filename != null
                ? `${process.env.PUBLIC_URL}/media/team/small/${
                    person.photo_filename
                  }`
                : require("../assets/img/user.png");
            return (
              <Card key={person.id} className="team">
                <OverlayTrigger
                  placement="auto"
                  overlay={
                    <Popover id={`popover_${person.id}`} title={person.name}>
                      {person.short_desc}
                    </Popover>
                  }>
                  <Image
                    roundedCircle
                    src={filename}
                    alt={filename}
                    onClick={() => this.handleclick(person.id)}
                  />
                </OverlayTrigger>
                <Card.Header>
                  <Card.Title>{person.name}</Card.Title>
                  <Card.Text>
                    {person.career.map((item, key) => {
                      return (
                        <span key={key}>
                          <OverlayTrigger
                            placement="auto"
                            overlay={
                              <Tooltip>{person.career_long[key]}</Tooltip>
                            }>
                            <span className="from">{item}</span>
                          </OverlayTrigger>
                          @
                          <OverlayTrigger
                            placement="top"
                            overlay={
                              <Tooltip>{person.school_long[key]}</Tooltip>
                            }>
                            <span className="from">{person.school[key]}</span>
                          </OverlayTrigger>
                          <br />
                        </span>
                      );
                    })}
                    <a
                      href={person.linkedin}
                      target="_blank"
                      alt="linkedin"
                      rel="noopener noreferrer">
                      <span className="fab fa-linkedin" />
                    </a>
                    <a
                      href={person.email ? `mailto:${person.email}` : null}
                      target="_blank"
                      alt="email"
                      rel="noopener noreferrer">
                      <span className="fas fa-envelope" />
                    </a>
                  </Card.Text>
                </Card.Header>
              </Card>
            );
          })}
        </CardDeck>
      );
    }
  }
}

export default Team_card;
