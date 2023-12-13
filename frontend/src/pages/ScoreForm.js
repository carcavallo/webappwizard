import React, { useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axios from 'axios';

const ScoreForm = () => {
  const navigate = useNavigate();
  const { id: patientId } = useParams();
  const [scoreData, setScoreData] = useState({
    criteria_1: null,
    criteria_2: null,
    criteria_3: null,
    criteria_4: null,
    criteria_5: null,
    criteria_6: null,
    criteria_7: null,
    criteria_8: null,
    criteria_9: null,
    criteria_10: null,
    criteria_11: null,
    criteria_12: null,
    criteria_13: null,
    criteria_14: null,
    criteria_15: null,
    criteria_16: null,
    criteria_17: null,
    criteria_18: null,
    criteria_19: null,
    criteria_20: null,
  });
  const [allCriteriaSet, setAllCriteriaSet] = useState(false);

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
      const payload = { patient_id: patientId, ...scoreData };

      const response = await axios.post('http://localhost/api/score', payload, {
        headers,
      });

      if (isIntermediateSave) {
        navigate('/dashboard');
      } else {
        navigate(`/patient/${patientId}/score/${response.data.id}/display`);
      }
    } catch (error) {
      console.error(error);
    }
  };

  const handleBack = () => {
    navigate(-1);
  };

  const renderCriteria = (number, question) => (
    <tr>
      <td>{question}</td>
      <td>
        <input
          type="radio"
          id={`criteria_${number}_yes`}
          name={`criteria_${number}`}
          value="yes"
          onChange={handleChange}
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
        />
        <label htmlFor={`criteria_${number}_no`}>Nein</label>
      </td>
    </tr>
  );

  return (
    <>
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
            className="btn btn-link mb-3 link custom-link"
            disabled={!allCriteriaSet}
          >
            Berechnen
          </button>
          {allCriteriaSet ? null : (
            <button
              type="button"
              className="btn btn-link mb-3 link custom-link"
              onClick={handleIntermediateSave}
            >
              Zwischenspeichern
            </button>
          )}
          <br />
          <button
            type="button"
            className="btn btn-link mb-3 link custom-link"
            onClick={handleBack}
          >
            Zurück
          </button>
        </form>
      </div>
    </>
  );
};

export default ScoreForm;
