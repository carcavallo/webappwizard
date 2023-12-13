import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { Button, Navbar, Container, Nav } from 'react-bootstrap';
import logo from '../assets/logo.png';
import './Navigation.css';

const NavBar = ({ username }) => {
  const navigate = useNavigate();
  const location = useLocation();

  const handleLogout = () => {
    localStorage.removeItem('userId');
    localStorage.removeItem('token');
    navigate('/');
  };

  const isNotHomePage = () => {
    const homePagePath = '/';
    return location.pathname !== homePagePath;
  };

  return (
    <>
      <div className="top-banner py-2">
        <Container className="d-flex justify-content-end">
          <Nav.Link
            href="https://ck-care.ch/"
            target="_blank"
            className="custom-link me-3"
          >
            HOMEPAGE
          </Nav.Link>
          <Nav.Link
            href="https://ck-care.ch/kontakt"
            target="_blank"
            className="custom-link me-3"
          >
            KONTAKT
          </Nav.Link>
          <Nav.Link
            href="https://ck-care.ch/links"
            target="_blank"
            className="custom-link"
          >
            LINKS
          </Nav.Link>
        </Container>
      </div>

      <Navbar bg="light" expand="lg" className="custom-navbar">
        <Container>
          <Navbar.Brand href="/">
            <img
              src={logo}
              height="45"
              className="d-inline-block align-top"
              alt="Company Logo"
            />
          </Navbar.Brand>
          {isNotHomePage() && (
            <Button
              variant="outline-link"
              onClick={handleLogout}
              className="logout-button custom-link"
            >
              Logout
            </Button>
          )}
        </Container>
      </Navbar>
    </>
  );
};

export default NavBar;
