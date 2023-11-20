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

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await axios.post('http://localhost/api/auth/user/register', formData);
      navigate.push('/login');
    } catch (error) {
      setError(error.response?.data?.message || 'An error occurred during registration.');
    }
  };

  return (
    <div className="container mt-5">
      <h1>Register</h1>
      {error && <div className="alert alert-danger" role="alert">{error}</div>}
      <form onSubmit={handleSubmit} className="mt-4">
        <div className="form-group">
          <label htmlFor="anrede">Salutation</label>
          <select className="form-control" id="anrede" name="anrede" value={formData.anrede} onChange={handleChange}>
            <option value="">Select Salutation</option>
            <option value="Herr">Mr.</option>
            <option value="Frau">Ms.</option>
          </select>
        </div>

        <div className="form-group">
          <label htmlFor="titel">Title</label>
          <input type="text" className="form-control" id="titel" name="titel" value={formData.titel} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="vorname">First Name</label>
          <input type="text" className="form-control" id="vorname" name="vorname" value={formData.vorname} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="nachname">Last Name</label>
          <input type="text" className="form-control" id="nachname" name="nachname" value={formData.nachname} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="email">Email Address</label>
          <input type="email" className="form-control" id="email" name="email" value={formData.email} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="arbeitsstelle_name">Workplace Name</label>
          <input type="text" className="form-control" id="arbeitsstelle_name" name="arbeitsstelle_name" value={formData.arbeitsstelle_name} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="arbeitsstelle_adresse">Workplace Address</label>
          <input type="text" className="form-control" id="arbeitsstelle_adresse" name="arbeitsstelle_adresse" value={formData.arbeitsstelle_adresse} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="arbeitsstelle_stadt">Workplace City</label>
          <input type="text" className="form-control" id="arbeitsstelle_stadt" name="arbeitsstelle_stadt" value={formData.arbeitsstelle_stadt} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="arbeitsstelle_plz">Workplace Postal Code</label>
          <input type="text" className="form-control" id="arbeitsstelle_plz" name="arbeitsstelle_plz" value={formData.arbeitsstelle_plz} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="arbeitsstelle_land">Workplace Country</label>
          <input type="text" className="form-control" id="arbeitsstelle_land" name="arbeitsstelle_land" value={formData.arbeitsstelle_land} onChange={handleChange} />
        </div>

        <div className="form-group">
          <label htmlFor="taetigkeitsbereich">Field of Activity</label>
          <select className="form-control" id="taetigkeitsbereich" name="taetigkeitsbereich" value={formData.taetigkeitsbereich} onChange={handleChange}>
            <option value="">Select Field of Activity</option>
            <option value="Patientenversorgung">Patient Care</option>
            <option value="Forschung">Research</option>
            <option value="Arzneimittelentwicklung">Pharmaceutical Development</option>
            <option value="Sonstiges">Other</option>
          </select>
        </div>

        <div className="form-group">
          <label htmlFor="taetigkeitsbereich_sonstiges">Other Activities</label>
          <input type="text" className="form-control" id="taetigkeitsbereich_sonstiges" name="taetigkeitsbereich_sonstiges" value={formData.taetigkeitsbereich_sonstiges} onChange={handleChange} />
        </div>

        <button type="submit" className="btn btn-primary">Register</button>
      </form>
    </div>
  );
};

export default RegistrationPage;
