import React, { useEffect, useState, useRef } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axios from 'axios';
import scoreScaleImage from '../assets/score.png';
import html2canvas from 'html2canvas';

const ScoreDisplay = () => {
  const navigate = useNavigate();
  const { id, score_id } = useParams();
  const [score, setScore] = useState({});
  const [patientId, setPatientId] = useState('');
  const [timestamp, setTimestamp] = useState('');
  const scoreRef = useRef(null);

  useEffect(() => {
    const fetchScoreAndPatientInfo = async () => {
      const token = localStorage.getItem('token');
      try {
        const scoreResponse = await axios.get(
          `http://localhost/api/scores/${id}`,
          {
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${token}`,
            },
          }
        );

        if (scoreResponse.data.status === 'success') {
          const matchedScore = scoreResponse.data.scores.find(
            s => s.id === parseInt(score_id)
          );
          if (matchedScore) {
            setScore(matchedScore);
            const dateParts = matchedScore.created_at.split(' ')[0].split('-');
            setTimestamp(
              `${dateParts[2]}.${dateParts[1]}.${dateParts[0].substring(2)}`
            );
          }

          const patientResponse = await axios.get(
            `http://localhost/api/patient/${id}`,
            {
              headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token}`,
              },
            }
          );
          if (patientResponse.data.status === 'success') {
            setPatientId(patientResponse.data.patientData.patient_id);
          }
        }
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    };

    fetchScoreAndPatientInfo();
  }, [id, score_id]);

  useEffect(() => {
    const takeScreenshot = async () => {
      if (scoreRef.current) {
        try {
          const canvas = await html2canvas(scoreRef.current);
          const blob = await new Promise((resolve, reject) => {
            canvas.toBlob(blob => {
              if (blob) {
                resolve(blob);
              } else {
                reject(new Error('Canvas to Blob conversion failed'));
              }
            });
          });
          await sendScreenshotToServer(blob);
        } catch (error) {
          console.error('Screenshot error:', error);
        }
      }
    };

    const sendScreenshotToServer = async blob => {
      const formData = new FormData();
      formData.append('screenshot', blob, 'screenshot.png');
      formData.append('patient_id', id);
      formData.append('timestamp', timestamp);

      const token = localStorage.getItem('token');
      try {
        await axios.post('http://localhost/api/upload-screenshot', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            Authorization: `Bearer ${token}`,
          },
        });

        await axios.get(`http://localhost/api/generate-pdf/${id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
      } catch (error) {
        console.error('Error uploading screenshot or generating PDF:', error);
      }
    };

    if (score && score.total_score !== undefined) {
      takeScreenshot();
    }
  }, [score, id, timestamp]);

  const handleBack = () => {
    navigate('/dashboard');
  };

  const getScorePosition = score => {
    const scaleStart = 10;
    const scaleEnd = 90;
    const scaleLength = scaleEnd - scaleStart;
    const minScore = -14.76;
    const maxScore = 12.63;
    const normalizedScore = (score - minScore) / (maxScore - minScore);
    const position = scaleStart + normalizedScore * scaleLength;
    return position;
  };

  return (
    <>
      <div className="container mt-5">
        <h1 className="mb-5">
          Berechneter Score für Patient {patientId} vom {timestamp}
        </h1>
        {score && score.total_score !== undefined ? (
          <div ref={scoreRef} style={{ position: 'relative', height: '315px' }}>
            <img
              src={scoreScaleImage}
              alt="Score Scale"
              style={{ width: '100%' }}
            />
            <div
              style={{
                position: 'absolute',
                left: `${getScorePosition(score.total_score)}%`,
                bottom: '40px',
              }}
            >
              <div
                style={{
                  width: '3px',
                  height: '105px',
                  backgroundColor: 'red',
                }}
              />
              <div
                style={{
                  position: 'absolute',
                  bottom: '107px',
                  color: 'red',
                  left: '50%',
                  transform: 'translateX(-50%)',
                  fontWeight: 'bold',
                  fontSize: '40px',
                }}
              >
                {score.total_score}
              </div>
            </div>
            <button
              type="button"
              className="btn btn-link mb-3"
              onClick={handleBack}
            >
              Zurück
            </button>
          </div>
        ) : (
          <p>Loading score...</p>
        )}
      </div>
    </>
  );
};

export default ScoreDisplay;
