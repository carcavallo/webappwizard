import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axios from 'axios';
import scoreScaleImage from '../assets/score.png';
import NavBar from '../components/Navigation';

const ScoreDisplay = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const [scores, setScores] = useState([]);
  const [patientId, setPatientId] = useState('');

  useEffect(() => {
    const fetchScores = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`http://localhost/api/scores/${id}`, {
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`,
          },
        });
        if (response.data.status === 'success') {
          setScores(response.data.scores);
        }
      } catch (error) {
        console.error(error);
      }
    };

    const fetchPatientInfo = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`http://localhost/api/patient/${id}`, {
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`,
          },
        });
        console.log(response);
        if (response.data.status === 'success') {
          setPatientId(response.data.patientData.patient_id);
        }
      } catch (error) {
        console.error(error);
      }
    };

    fetchScores();
    fetchPatientInfo();
  }, [id]);

  const handleBack = () => {
    navigate('/dashboard');
  };

  const getScorePosition = score => {
    const maxScore = 12;
    const scoreOffset = (100 * (parseFloat(score) + maxScore)) / (2 * maxScore);
    return scoreOffset;
  };

  return (
    <>
      <NavBar />
      <div className="container mt-5">
        <h1 className="mb-5">Berrechneter Score für Patient: {patientId}</h1>
        {scores.length > 0 ? (
          <div style={{ position: 'relative', height: '192px' }}>
            <img
              src={scoreScaleImage}
              alt="Score Scale"
              style={{ width: '100%' }}
            />
            {scores.map((score, index) => (
              <div
                key={index}
                style={{
                  position: 'absolute',
                  left: `${getScorePosition(score.total_score)}%`,
                  bottom: '-40px',
                }}
              >
                <div
                  style={{
                    width: '5px',
                    height: '89px',
                    backgroundColor: 'red',
                  }}
                />
                <div
                  style={{
                    position: 'absolute',
                    bottom: '90px',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    color: 'black',
                    fontWeight: 'bold',
                  }}
                >
                  {score.total_score}
                </div>
              </div>
            ))}
            <button
              type="button"
              className="btn btn-link mb-3"
              onClick={handleBack}
            >
              Zurück
            </button>
          </div>
        ) : (
          <p>Loading scores...</p>
        )}
      </div>
    </>
  );
};

export default ScoreDisplay;
