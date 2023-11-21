import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const DashboardPage = () => {
  const [patients, setPatients] = useState([]);
  const navigate = useNavigate();
  const userId = localStorage.getItem('userId');

  useEffect(() => {
    const fetchPatients = async () => {
      try {
        const response = await axios.get(
          `http://localhost/api/user/${userId}/patients`,
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
          }
        );
        if (response.data.status === 'success') {
          setPatients(response.data.data);
        }
      } catch (error) {
        console.error('Fehler beim Abrufen der Patientendaten:', error);
      }
    };

    fetchPatients();
  }, [userId]);

  const handleAddPatient = () => {
    navigate('/patient');
  };

  const handleLogout = () => {
    localStorage.removeItem('userId');
    localStorage.removeItem('token');
    navigate('/');
  };

  const handlePatientClick = patientId => {
    navigate(`/patient/${patientId}/score`);
  };

  return (
    <div className="container mt-5">
      <h1>Patientenliste</h1>
      {patients.length > 0 ? (
        <ul className="list-group">
          {patients.map((patient, index) => (
            <li
              key={index}
              className="list-group-item list-group-item-action"
              onClick={() => handlePatientClick(patient.id)}
              style={{ cursor: 'pointer' }}
            >
              {patient.patient_id}
            </li>
          ))}
        </ul>
      ) : (
        <p>Es wurden noch keine Patienten erfasst.</p>
      )}
      <button onClick={handleAddPatient} className="btn btn-primary mt-3">
        Patienten registrieren
      </button>
      <br />
      <button onClick={handleLogout} className="btn btn-secondary mt-3">
        Logout
      </button>
    </div>
  );
};

export default DashboardPage;
