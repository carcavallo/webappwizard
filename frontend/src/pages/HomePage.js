import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const HomePage = () => {
  const [credentials, setCredentials] = useState({ email: '', password: '' });
  const navigate = useNavigate();

  const handleChange = (e) => {
    const { name, value } = e.target;
    setCredentials({ ...credentials, [name]: value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await axios.post('http://localhost/api/auth/user/login', credentials);
      localStorage.setItem('token', response.data.token);
      navigate.push('/dashboard');
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div className="container">
      <h1 className="text-center">Flip-Flop-App</h1>
       <p className="lead text-center">Welcome to the Flip-Flop-App, please log in to continue.</p>
        <div className="row justify-content-center">
          <div className="col-6">
            <form onSubmit={handleSubmit} className="mt-4">
            <div className="form-group">
                    <input type="email" name="email" className="form-control" placeholder="Email" onChange={handleChange} />
                    </div>
                    <div className="form-group">
                    <input type="password" name="password" className="form-control" placeholder="Password" onChange={handleChange} />
                    </div>
                    <div className="text-center">
                    <button type="submit" className="btn btn-primary">Login</button>
                    </div>
                </form>
                <div className="text-center mt-3">
                    <a href="/register">Don't have an account? Register</a>
                </div>
            </div>
        </div>
    </div>
  );
};

export default HomePage;
