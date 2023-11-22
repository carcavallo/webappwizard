import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const RegistrationPage = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    anrede: '',
    titel: '',
    vorname: '',
    nachname: '',
    email: '',
    arbeitsstelle_name: '',
    arbeitsstelle_adresse: '',
    arbeitsstelle_stadt: '',
    arbeitsstelle_plz: '',
    arbeitsstelle_land: '',
    taetigkeitsbereich: '',
    taetigkeitsbereich_sonstiges: '',
  });
  const [error, setError] = useState('');

  const handleChange = e => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const validateForm = () => {
    if (
      !formData.anrede ||
      !formData.vorname ||
      !formData.nachname ||
      !formData.email ||
      !formData.arbeitsstelle_name ||
      !formData.arbeitsstelle_adresse ||
      !formData.arbeitsstelle_stadt ||
      !formData.arbeitsstelle_plz ||
      !formData.arbeitsstelle_land ||
      !formData.taetigkeitsbereich
    ) {
      setError('Bitte füllen Sie alle Felder aus.');
      return false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
      setError('Bitte geben Sie eine gültige E-Mail-Adresse ein.');
      return false;
    }

    if (isNaN(formData.arbeitsstelle_plz)) {
      setError('Bitte geben Sie eine gültige PLZ ein (nur Zahlen).');
      return false;
    }

    if (
      formData.taetigkeitsbereich === 'Sonstiges' &&
      !formData.taetigkeitsbereich_sonstiges
    ) {
      setError('Bitte füllen Sie das Feld "Tätigkeitsbereich Sonstiges" aus.');
      return false;
    }

    setError('');
    return true;
  };

  const handleSubmit = async e => {
    e.preventDefault();
    if (!validateForm()) return;

    try {
      await axios.post('http://localhost/api/auth/user/register', formData);
      navigate('/?registrationSuccess=true');
    } catch (error) {
      setError(
        error.response?.data?.message ||
          'Ein Fehler ist bei der Registrierung aufgetreten.'
      );
    }
  };

  return (
    <div className="container mt-5">
      <h1>Registrieren</h1>
      {error && (
        <div className="alert alert-danger" role="alert">
          {error}
        </div>
      )}
      <form onSubmit={handleSubmit} className="mt-4">
        <div className="form-group mb-3">
          <label htmlFor="anrede">Anrede</label>
          <select
            className="form-control"
            id="anrede"
            name="anrede"
            value={formData.anrede}
            onChange={handleChange}
          >
            <option value="">Anrede auswählen</option>
            <option value="Herr">Herr</option>
            <option value="Frau">Frau</option>
          </select>
        </div>

        <div className="form-group mb-3">
          <label htmlFor="titel">Titel</label>
          <select
            className="form-control"
            id="titel"
            name="titel"
            value={formData.titel}
            onChange={handleChange}
          >
            <option value="">Titel auswählen</option>
            <option value="Prof.">Prof.</option>
            <option value="Dr.">Dr.</option>
          </select>
        </div>

        <div className="form-group mb-3">
          <label htmlFor="vorname">Vorname</label>
          <input
            type="text"
            className="form-control"
            id="vorname"
            name="vorname"
            value={formData.vorname}
            onChange={handleChange}
          />
        </div>

        <div className="form-group mb-3">
          <label htmlFor="nachname">Nachname</label>
          <input
            type="text"
            className="form-control"
            id="nachname"
            name="nachname"
            value={formData.nachname}
            onChange={handleChange}
          />
        </div>

        <div className="form-group mb-3">
          <label htmlFor="email">Email Adresse</label>
          <input
            type="email"
            className="form-control"
            id="email"
            name="email"
            value={formData.email}
            onChange={handleChange}
          />
        </div>

        <div className="form-group mb-3">
          <label htmlFor="arbeitsstelle_name">Arbeitsstelle</label>
          <input
            type="text"
            className="form-control"
            id="arbeitsstelle_name"
            name="arbeitsstelle_name"
            value={formData.arbeitsstelle_name}
            onChange={handleChange}
          />
        </div>

        <div className="form-group mb-3">
          <label htmlFor="arbeitsstelle_adresse">Arbeitsstelle Adresse</label>
          <input
            type="text"
            className="form-control"
            id="arbeitsstelle_adresse"
            name="arbeitsstelle_adresse"
            value={formData.arbeitsstelle_adresse}
            onChange={handleChange}
          />
        </div>

        <div className="form-group mb-3">
          <label htmlFor="arbeitsstelle_stadt">Arbeitsstelle Stadt</label>
          <input
            type="text"
            className="form-control"
            id="arbeitsstelle_stadt"
            name="arbeitsstelle_stadt"
            value={formData.arbeitsstelle_stadt}
            onChange={handleChange}
          />
        </div>

        <div className="form-group mb-3">
          <label htmlFor="arbeitsstelle_plz">Arbeitsstelle PLZ</label>
          <input
            type="text"
            className="form-control"
            id="arbeitsstelle_plz"
            name="arbeitsstelle_plz"
            value={formData.arbeitsstelle_plz}
            onChange={handleChange}
          />
        </div>

        <div className="form-group mb-3">
          <label htmlFor="arbeitsstelle_land">Arbeitsstelle Land</label>
          <input
            type="text"
            className="form-control"
            id="arbeitsstelle_land"
            name="arbeitsstelle_land"
            value={formData.arbeitsstelle_land}
            onChange={handleChange}
          />
        </div>

        <div className="form-group mb-3">
          <label htmlFor="taetigkeitsbereich">Tätigkeitsbereich</label>
          <select
            className="form-control"
            id="taetigkeitsbereich"
            name="taetigkeitsbereich"
            value={formData.taetigkeitsbereich}
            onChange={handleChange}
          >
            <option value="">Tätigkeitsbereich auswählen</option>
            <option value="Patientenversorgung">Patientenversorgung</option>
            <option value="Forschung">Forschung</option>
            <option value="Arzneimittelentwicklung">
              Arzneimittelentwicklung
            </option>
            <option value="Sonstiges">Sonstiges</option>
          </select>
        </div>

        {formData.taetigkeitsbereich === 'Sonstiges' && (
          <div className="form-group mb-3">
            <label htmlFor="taetigkeitsbereich_sonstiges">Sonstiges</label>
            <input
              type="text"
              className="form-control"
              id="taetigkeitsbereich_sonstiges"
              name="taetigkeitsbereich_sonstiges"
              value={formData.taetigkeitsbereich_sonstiges}
              onChange={handleChange}
            />
          </div>
        )}

        <div className="form-group form-check mb-3">
          <input
            type="checkbox"
            className="form-check-input"
            id="datenschutzCheckbox"
            required
          />
          <label className="form-check-label" htmlFor="datenschutzCheckbox">
            Ich bin einverstanden, dass meine Daten zur Bearbeitung der Anfrage
            zur Nutzung des Flip-Flop-Rechners gespeichert werden dürfen. Die
            Informationen zum Datenschutz habe ich gelesen (
            <a href="https://ck-care.ch/datenschutz/">DATENSCHUTZ | CK-CARE</a>
            ).
          </label>
        </div>

        <button type="submit" className="btn btn-link mb-3">
          Abschicken
        </button>
      </form>
    </div>
  );
};

export default RegistrationPage;
