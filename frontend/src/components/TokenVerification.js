import React, { useEffect } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

const TokenVerification = ({ children }) => {
  const navigate = useNavigate();

  useEffect(() => {
    const verifyToken = async () => {
      const token = localStorage.getItem('token');
      if (!token) {
        navigate('/');
        return;
      }

      try {
        const response = await axios.post(
          'http://localhost/api/auth/validate-token',
          { token }
        );

        if (response.status === 200) {
          return;
        } else {
          localStorage.removeItem('userId');
          localStorage.removeItem('token');
          navigate('/');
        }
      } catch (error) {
        localStorage.removeItem('userId');
        localStorage.removeItem('token');
        navigate('/');
      }
    };

    verifyToken();
  }, [navigate]);

  return <>{children}</>;
};

export default TokenVerification;
