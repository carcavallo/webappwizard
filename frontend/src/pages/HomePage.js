import React, { useState, useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';

const HomePage = () => {
  const [credentials, setCredentials] = useState({ email: '', password: '' });
  const [loginError, setLoginError] = useState('');
  const navigate = useNavigate();
  const location = useLocation();
  const [registrationSuccess, setRegistrationSuccess] = useState(false);

  useEffect(() => {
    const searchParams = new URLSearchParams(location.search);
    if (searchParams.get('registrationSuccess') === 'true') {
      setRegistrationSuccess(true);
    }
    const userId = localStorage.getItem('userId');
    const token = localStorage.getItem('token');
    if (userId && token) {
      navigate('/dashboard');
    }
  }, [navigate, location]);

  const handleChange = e => {
    const { name, value } = e.target;
    setCredentials({ ...credentials, [name]: value });
  };

  const handleSubmit = async e => {
    e.preventDefault();
    setLoginError('');
    try {
      const response = await axios.post(
        'http://localhost/api/auth/user/login',
        credentials
      );

      if (response.data.status === 'success' && response.data.token) {
        localStorage.setItem('userId', response.data.userId);
        localStorage.setItem('token', response.data.token);
        navigate('/dashboard');
      } else {
        setLoginError(
          'Login fehlgeschlagen. Bitte überprüfen Sie Ihre E-Mail und Ihr Passwort.'
        );
      }
    } catch (error) {
      setLoginError(
        'Login fehlgeschlagen. Bitte überprüfen Sie Ihre E-Mail und Ihr Passwort.'
      );
      console.error(error);
    }
  };

  return (
    <div
      className="d-flex justify-content-center align-items-center"
      style={{ height: '100vh' }}
    >
      <div className="text-center">
        <h1>Flip-Flop-App</h1>
        <p className="lead">
          Willkommen zu der Flip-Flop-App, bitte loggen Sie sich ein, um
          fortzufahren.
        </p>
        {registrationSuccess && (
          <div className="alert alert-success" role="alert">
            Danke für Ihre Anmeldung, wir werden Ihre Daten prüfen und Ihnen
            eine Email mit den Zugangsdaten schicken.
          </div>
        )}
        {loginError && (
          <div className="alert alert-danger" role="alert">
            {loginError}
          </div>
        )}
        <div className="d-inline-block">
          <form onSubmit={handleSubmit}>
            <div className="form-group mb-2">
              <input
                type="email"
                name="email"
                className="form-control"
                placeholder="Email"
                value={credentials.email}
                onChange={handleChange}
              />
            </div>
            <div className="form-group mb-3">
              <input
                type="password"
                name="password"
                className="form-control"
                placeholder="Passwort"
                value={credentials.password}
                onChange={handleChange}
              />
            </div>
            <button type="submit" className="btn btn-link">
              Login
            </button>
          </form>
          <div className="mt-3">
            <a href="/register">Sie haben noch kein Konto? Registrieren</a>
          </div>
        </div>
      </div>
    </div>
  );
};

export default HomePage;
