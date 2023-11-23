import React, { useEffect } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

const TokenVerification = ({ children }) => {
  const navigate = useNavigate();

  useEffect(() => {
    const refreshToken = async oldToken => {
      try {
        const response = await axios.post(
          'http://localhost/api/auth/refresh-token',
          { token: oldToken }
        );

        if (response.status === 200 && response.data.newToken) {
          localStorage.setItem('token', response.data.newToken);
          return true;
        }
      } catch (error) {
        console.error('Error refreshing token:', error);
      }
      return false;
    };

    const verifyToken = async () => {
      const token = localStorage.getItem('token');
      if (!token) {
        navigate('/');
        return;
      }

      try {
        const validationResponse = await axios.post(
          'http://localhost/api/auth/validate-token',
          { token }
        );

        if (
          validationResponse.status === 200 &&
          validationResponse.data.valid
        ) {
          return;
        } else {
          const refreshSuccess = await refreshToken(token);
          if (!refreshSuccess) {
            localStorage.removeItem('id');
            localStorage.removeItem('token');
            navigate('/');
          }
        }
      } catch (error) {
        localStorage.removeItem('id');
        localStorage.removeItem('token');
        navigate('/');
      }
    };

    verifyToken();
  }, [navigate]);

  return <>{children}</>;
};

export default TokenVerification;
