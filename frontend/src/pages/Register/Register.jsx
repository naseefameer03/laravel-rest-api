import { useState } from "react";
import api from "../../api/axios";

export default function Register() {
  const [form, setForm] = useState({ name: "", email: "", password: "", password_confirmation: "" });
  const [message, setMessage] = useState("");

  const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value });

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await api.post("/register", form);
      setMessage("Registered successfully. Please login.");
    } catch (err) {
      setMessage("Error registering user.");
    }
  };

  return (
    <div>
      <h2>Register</h2>
      <form onSubmit={handleSubmit}>
        <input name="name" placeholder="Name" onChange={handleChange} />
        <input name="email" placeholder="Email" onChange={handleChange} />
        <input type="password" name="password" placeholder="Password" onChange={handleChange} />
        <input type="password" name="password_confirmation" placeholder="Confirm Password" onChange={handleChange} />
        <button type="submit">Register</button>
      </form>
      <p>{message}</p>
    </div>
  );
}
