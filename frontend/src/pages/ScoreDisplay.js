import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import scoreScaleImage from '../assets/score.png'; // Ensure this path is correct

const ScoreDisplay = () => {
  const { id } = useParams();
  const [score, setScore] = useState(null);

  useEffect(() => {
    const fetchScores = async () => {
      try {
        const token = localStorage.getItem('token'); // Retrieve the token from localStorage or context
        const response = await axios.get(`http://localhost/api/scores/${id}`, {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
        });
        if (response.data.status === 'success') {
          // Set the score using the first element's total_score from the response
          setScore(response.data.scores[0].total_score);
        }
      } catch (error) {
        console.error(error);
        // Handle error, e.g., set an error state or message
      }
    };

    fetchScores();
  }, [id]);

  const getScorePosition = (score) => {
    // Adjust this logic if needed based on how you want to display the score
    const maxScore = 12; // Adjust the maximum score if necessary
    const scoreOffset = 100 * (parseFloat(score) + maxScore) / (2 * maxScore);
    return scoreOffset;
  };

  const scorePosition = score !== null ? getScorePosition(score) : 0;

  return (
    <div className="container mt-5">
      {score !== null ? (
        <div style={{ position: 'relative', height: '192px' }}> {/* Adjusted for the given height */}
          <img src={scoreScaleImage} alt="Score Scale" style={{ width: '100%' }} />
          <div
            style={{
              position: 'absolute',
              left: `${scorePosition}%`,
              bottom: '-40px', // Align with the bottom of the scale
            }}
          >
            {/* Score indicator, e.g., a vertical line */}
            <div style={{ width: '5px', height: '89px', backgroundColor: 'red' }} />
            {/* Score value, aligned with the other scale numbers */}
            <div style={{
              position: 'absolute',
              bottom: '90px', // Adjust this value as needed
              left: '50%',
              transform: 'translateX(-50%)',
              color: 'black',
              fontWeight: 'bold'
            }}>
              {score}
            </div>
          </div>
        </div>
      ) : (
        <p>Loading score...</p>
      )}
    </div>
  );
};

export default ScoreDisplay;
