import React from 'react';
import scoreBackground from '../assets/Bild2.png'; // Ensure this path points to where your image is located

const ScoreDisplay = ({ score }) => {
  const calculatePosition = (score) => {
    const scaleMin = -12;
    const scaleMax = 12;
    const scaleFactor = 100 / (scaleMax - scaleMin); 
    return (score - scaleMin) * scaleFactor;
  };

  const scoreMarkerStyle = (score) => ({
    position: 'absolute',
    top: '50%',
    left: `${calculatePosition(score)}%`,
    transform: 'translate(-50%, -50%)',
  });

  return (
    <div style={{ position: 'relative', width: '100%', height: '200px', backgroundImage: `url(${scoreBackground})`, backgroundSize: 'cover' }}>
      <div style={scoreMarkerStyle(score)}>
        <div style={{ width: '10px', height: '10px', borderRadius: '50%', backgroundColor: 'red' }}></div>
      </div>
    </div>
  );
};

export default ScoreDisplay;
