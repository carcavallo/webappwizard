import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axios from 'axios';
import NavBar from '../components/Navigation';

const EditScoreForm = () => {
  const navigate = useNavigate();
  const { id: patientId } = useParams();
  const { score_id: scoreId } = useParams();
  const [scoreData, setScoreData] = useState({});
  const [allCriteriaSet, setAllCriteriaSet] = useState(false);

  useEffect(() => {
    const loadScores = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(
          `http://localhost/api/scores/${patientId}`,
          {
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${token}`,
            },
          }
        );
        if (response.data && Array.isArray(response.data.scores)) {
          const scoreWithId = response.data.scores.find(
            score => score.id === parseInt(scoreId)
          );
          setScoreData(scoreWithId);
        }
      } catch (error) {
        console.error('Error fetching scores:', error);
      }
    };

    loadScores();
  }, [patientId, scoreId]);

  const handleChange = e => {
    const value = e.target.value === 'yes' ? 1 : 0;
    const newScoreData = { ...scoreData, [e.target.name]: value };
    setScoreData(newScoreData);
    const allSet = Object.values(newScoreData).every(
      criteria => criteria !== null
    );
    setAllCriteriaSet(allSet);
  };

  const handleIntermediateSave = () => {
    handleSubmit(null, true);
  };

  const handleSubmit = async (e, isIntermediateSave = false) => {
    if (e) e.preventDefault();
    try {
      const token = localStorage.getItem('token');
      const headers = {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      };

      let payload = {};

      for (const [key, value] of Object.entries(scoreData)) {
        if (
          key !== 'saved' &&
          key !== 'patient_id' &&
          key !== 'id' &&
          (value === 0 || value === 1)
        ) {
          payload[key] = value;
        }
      }

      await axios.put(`http://localhost/api/score/${scoreId}`, payload, {
        headers,
      });

      if (isIntermediateSave) {
        navigate('/dashboard');
      } else {
        navigate(`/patient/${patientId}/score/${scoreId}/display`);
      }
    } catch (error) {
      console.error(error);
    }
  };

  const handleBack = () => {
    navigate(-1);
  };

  const renderCriteria = (number, question) => {
    const criteriaValue = scoreData[`criteria_${number}`];

    return (
      <tr>
        <td>{question}</td>
        <td>
          <input
            type="radio"
            id={`criteria_${number}_yes`}
            name={`criteria_${number}`}
            value="yes"
            onChange={handleChange}
            checked={criteriaValue === 1}
          />
          <label htmlFor={`criteria_${number}_yes`}>Ja</label>
        </td>
        <td>
          <input
            type="radio"
            id={`criteria_${number}_no`}
            name={`criteria_${number}`}
            value="no"
            onChange={handleChange}
            checked={criteriaValue === 0}
          />
          <label htmlFor={`criteria_${number}_no`}>Nein</label>
        </td>
      </tr>
    );
  };

  return (
    <>
      <NavBar />
      <div className="container mt-5">
        <h1 className="mb-4">Flip-Flop-Score Formular</h1>
        <form onSubmit={handleSubmit}>
          <div className="row">
            <div className="col-md-6">
              <h2>Anamnesekriterien</h2>
              <table className="table">
                <tbody>
                  {renderCriteria(
                    1,
                    '(1) Positive Eigenanamnese für Atopie (Vorliegen von min. 1 Erkrankung: Asthma, AD, allergische Rhinitis, allergische Konjunktivitis)'
                  )}
                  {renderCriteria(
                    2,
                    '(2) Bek. Sensibilisierungen und/oder Nahrungsmittelunverträglichkeiten'
                  )}
                  {renderCriteria(
                    3,
                    '(3) Positive Familienanamnese für Atopie (Vorliegen von min. 1 Erkrankung: Asthma, AD, allergische Rhinitis, allergische Konjunktivitis, „Ekzeme“)'
                  )}
                  {renderCriteria(
                    4,
                    '(4) Positive Familienanamnese für Psoriasis'
                  )}
                  {renderCriteria(
                    5,
                    '(5) Abrupte Exazerbation nach Absetzen einer systemischen Steroidtherapie'
                  )}
                  {renderCriteria(6, '(6) Gelenkschmerzen')}
                  {renderCriteria(
                    7,
                    '(7) Daktylitis und/oder Enthesiopathien (v.a. im Bereich der Achillessehne)'
                  )}
                </tbody>
              </table>
            </div>
            <div className="col-md-6">
              <h2>Untersuchungskriterien</h2>
              <table className="table">
                <tbody>
                  {renderCriteria(
                    8,
                    '(8) Dishydrosis (aktuell oder in der Vergangenheit)'
                  )}
                  {renderCriteria(
                    9,
                    '(9) Pusteln (aktuell oder in der Vergangenheit; ausser im Gesicht)'
                  )}
                  {renderCriteria(
                    10,
                    '(10) Psoriasis-typische Nagelveränderungen (Tüpfel, Ölfleck, Dystrophie)'
                  )}
                  {renderCriteria(11, '(11) Palmare Hyperlinearität')}
                  {renderCriteria(
                    12,
                    '(12) Kopfhautbefall vom Capillitium über die Stirn-Haargrenze hinaus'
                  )}
                  {renderCriteria(
                    13,
                    '(13) Dennie-Morgan-Falte u./od. periorbitale Verschattung u./od. halonierte Augen'
                  )}
                  {renderCriteria(
                    14,
                    '(14) Perlèche u./od. Cheilitis (aktuell oder in der Vergangenheit)'
                  )}
                  {renderCriteria(
                    15,
                    '(15) Plaques-Befall der Retroaurikulärregion'
                  )}
                  {renderCriteria(
                    16,
                    '(16) Head Neck Dermatitis oder Dirty neck'
                  )}
                  {renderCriteria(17, '(17) Keratosis pilaris')}
                  {renderCriteria(
                    18,
                    '(18) Erythematosquamöse Plaques an Körper u./od. Extremitäten (aktuell oder in der Vergangenheit)'
                  )}
                  {renderCriteria(
                    19,
                    '(19) Ekzeme u./od. Lichenifizierung der Beugen (aktuell oder in der Vergangenheit)'
                  )}
                  {renderCriteria(
                    20,
                    '(20) Befall der Rima ani (Erythem und/oder Mazeration)'
                  )}
                </tbody>
              </table>
            </div>
          </div>
          <button
            type="submit"
            className="btn btn-link mb-3"
            disabled={!allCriteriaSet}
          >
            Berechnen
          </button>
          {allCriteriaSet ? null : (
            <button
              type="button"
              className="btn btn-link mb-3"
              onClick={handleIntermediateSave}
            >
              Zwischenspeichern
            </button>
          )}
          <br />
          <button
            type="button"
            className="btn btn-link mb-3"
            onClick={handleBack}
          >
            Zurück
          </button>
        </form>
      </div>
    </>
  );
};

export default EditScoreForm;
