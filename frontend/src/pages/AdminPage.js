import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const AdminPage = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const handleLogin = async e => {
    e.preventDefault();
    setError('');

    try {
      const response = await axios.post(
        'http://localhost/api/auth/admin/login',
        {
          email: email,
          password: password,
        }
      );

      if (response.status === 200 && response.data.token) {
        localStorage.setItem('adminToken', response.data.token);
        navigate('/admin/dashboard');
      }
    } catch (error) {
      if (error.response) {
        setError(error.response.data.message);
      } else {
        setError('Login failed. Please try again.');
      }
    }
  };

  return (
    <div
      className="d-flex justify-content-center align-items-center"
      style={{ height: '100vh' }}
    >
      <div className="text-center">
        <h1>Admin Login</h1>
        {error && <div className="alert alert-danger">{error}</div>}
        <div className="d-inline-block mt-2">
          <form onSubmit={handleLogin}>
            <div className="form-group mb-2">
              <input
                type="email"
                className="form-control"
                placeholder="Email"
                value={email}
                onChange={e => setEmail(e.target.value)}
              />
            </div>
            <div className="form-group mb-3">
              <input
                type="password"
                className="form-control"
                placeholder="Passwort"
                value={password}
                onChange={e => setPassword(e.target.value)}
              />
            </div>
            <button type="submit" className="btn btn-link link custom-link">
              Login
            </button>
          </form>
        </div>
      </div>
    </div>
  );
};

export default AdminPage;
