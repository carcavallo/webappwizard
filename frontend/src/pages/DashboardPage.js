import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import NavBar from '../components/Navigation';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faLock, faPen, faTrash } from '@fortawesome/free-solid-svg-icons';

const DashboardPage = () => {
  const [patients, setPatients] = useState([]);
  const token = localStorage.getItem('token');

  useEffect(() => {
    const fetchPatientsAndScores = async () => {
      try {
        const patientsResponse = await axios.get(
          `http://localhost/api/user/${localStorage.getItem(
            'userId'
          )}/patients`,
          { headers: { Authorization: `Bearer ${token}` } }
        );

        if (patientsResponse.data.status === 'success') {
          const scorePromises = patientsResponse.data.data.map(patient =>
            axios
              .get(`http://localhost/api/scores/${patient.id}`, {
                headers: {
                  'Content-Type': 'application/json',
                  Authorization: `Bearer ${token}`,
                },
              })
              .catch(error => ({ error }))
          );

          const scoresResponses = await Promise.all(scorePromises);

          const patientsWithScores = patientsResponse.data.data.map(
            (patient, index) => {
              const scoresResponse = scoresResponses[index];
              if (
                scoresResponse &&
                !scoresResponse.error &&
                scoresResponse.data.status === 'success'
              ) {
                return { ...patient, scores: scoresResponse.data.scores };
              } else {
                return { ...patient, scores: [] };
              }
            }
          );

          setPatients(patientsWithScores);
        }
      } catch (error) {
        console.error('Fehler beim Abrufen der Patientendaten:', error);
      }
    };

    fetchPatientsAndScores();
  }, [token]);

  const handleDeletePatient = async patientId => {
    try {
      await axios.delete(`http://localhost/api/patient/${patientId}`, {
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
      });
      setPatients(patients.filter(patient => patient.id !== patientId));
    } catch (error) {
      console.error('Fehler beim Löschen des Patienten:', error);
    }
  };

  const hasSavedScores = patient => {
    return patient.scores.some(score => score.saved);
  };

  const renderScoreWithIcon = (patientId, score) => {
    if (score.saved) {
      return (
        <span key={score.id} className="me-2">
          {score.total_score} <FontAwesomeIcon icon={faLock} />
        </span>
      );
    } else {
      return (
        <span key={score.id} className="me-2">
          <Link to={`/patient/${patientId}/score/${score.id}/edit`}>
            {score.total_score} <FontAwesomeIcon icon={faPen} />
          </Link>
        </span>
      );
    }
  };

  const renderScoreCalculationLink = patient => {
    if (patient.scores && patient.scores.length >= 4) {
      return null;
    }

    return (
      <Link
        to={`/patient/${patient.id}/score`}
        className="btn btn-link link custom-link"
      >
        Score berechnen
      </Link>
    );
  };

  return (
    <>
      <NavBar />
      <div className="container mt-5">
        <h1 className="mb-4">Patientenliste</h1>
        <table className="table">
          <thead>
            <tr>
              <th scope="col">Patienten-ID</th>
              <th scope="col">Scores</th>
              <th scope="col" className="align-middle ps-4">
                Aktionen
              </th>
            </tr>
          </thead>
          <tbody>
            {patients.map(patient => (
              <tr key={patient.id}>
                <td className="align-middle">{patient.patient_id}</td>
                <td className="align-middle">
                  {patient.scores && patient.scores.length > 0
                    ? patient.scores.map(score =>
                        renderScoreWithIcon(patient.id, score)
                      )
                    : 'Keine Scores'}
                </td>
                <td className="align-middle">
                  {renderScoreCalculationLink(patient)}
                  <Link
                    to={`/patient/${patient.id}/edit`}
                    className="btn btn-link me-2 link custom-link"
                  >
                    Patient Editieren
                  </Link>
                  {!hasSavedScores(patient) && (
                    <button
                      onClick={() => handleDeletePatient(patient.id)}
                      className="btn btn-link text-danger"
                      style={{ color: 'red' }}
                    >
                      <FontAwesomeIcon icon={faTrash} />
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        <Link to="/patient" className="btn btn-link mt-3 link custom-link">
          Patienten registrieren
        </Link>
      </div>
    </>
  );
};

export default DashboardPage;
