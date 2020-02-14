import React from "react";
import { Tab, Nav } from "react-bootstrap";
import Team from "./team_card";
import "./team_tabs.css";

const TeamTabs = ({ callback }) => {
  return (
    <Tab.Container defaultActiveKey="current">
      <Nav className="teamtab">
        <Nav.Item>
          <Nav.Link eventKey="current">
            <h1>Las Promesas</h1>
          </Nav.Link>
        </Nav.Item>
        <Nav.Item>
          <Nav.Link eventKey="graduate">
            <h1>Los Egresados</h1>
          </Nav.Link>
        </Nav.Item>
      </Nav>
      <hr />
      <Tab.Content>
        <Tab.Pane eventKey="current">
          <Team callback={callback}/>
        </Tab.Pane>
        <Tab.Pane eventKey="graduate">
          <Team callback={callback} status="graduate" />
        </Tab.Pane>
      </Tab.Content>
    </Tab.Container>
  );
};

export default TeamTabs;
