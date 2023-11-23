import React, { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import axios from 'axios';
import NavBar from '../components/Navigation';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faLock, faPen } from '@fortawesome/free-solid-svg-icons';

const DashboardPage = () => {
  const [patients, setPatients] = useState([]);
  const navigate = useNavigate();
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

  const handleAddPatient = () => {
    navigate('/patient');
  };

  const handleEditPatient = (e, patientId) => {
    e.stopPropagation();
    navigate(`/patient/${patientId}/edit`);
  };

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
      <Link to={`/patient/${patient.id}/score`} className="btn btn-link">
        Score berechnen
      </Link>
    );
  };

  return (
    <>
      <NavBar />
      <div className="container mt-5">
        <h1 className="mb-3">Patientenliste</h1>
        <ul className="list-group">
          <li className="list-group-item d-flex justify-content-between">
            <div className="flex-grow-1">
              <strong>Patienten-ID</strong>
            </div>
          </li>
          {patients.map(patient => (
            <li
              key={patient.id}
              className="list-group-item d-flex justify-content-between"
            >
              <div className="flex-grow-1">{patient.patient_id}</div>
              <div className="flex-grow-1 d-flex justify-content-start">
                {patient.scores && patient.scores.length > 0 ? (
                  <div className="d-flex justify-content-start">
                    {patient.scores.map(score =>
                      renderScoreWithIcon(patient.id, score)
                    )}
                  </div>
                ) : (
                  <div>Keine Scores</div>
                )}
              </div>
              <div className="flex-grow-1 d-flex justify-content-end">
                {renderScoreCalculationLink(patient)}
                <button
                  onClick={e => handleEditPatient(e, patient.id)}
                  className="btn btn-link me-2"
                >
                  Patient Editieren
                </button>
                {!hasSavedScores(patient) && (
                  <button
                    onClick={() => handleDeletePatient(patient.id)}
                    className="btn btn-link text-danger"
                  >
                    Patient Löschen
                  </button>
                )}
              </div>
            </li>
          ))}
        </ul>
        <button onClick={handleAddPatient} className="btn btn-link mt-3">
          Patienten registrieren
        </button>
      </div>
    </>
  );
};

export default DashboardPage;
