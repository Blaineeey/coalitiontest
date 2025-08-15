import React, { useState, useEffect } from 'react';
import axios from 'axios';

export default function ProductPage() {
    const [products, setProducts] = useState([]);
    const [form, setForm] = useState({ name: '', quantity: '', price: '' });
    const [editing, setEditing] = useState(null);

    useEffect(() => {
        fetchProducts();
    }, []);

    const fetchProducts = async () => {
        const res = await axios.get('/api/products');
        setProducts(res.data);
    };

    const handleChange = e => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async e => {
        e.preventDefault();

        if (editing) {
            await axios.put(`/api/products/${editing}`, form);
            setEditing(null);
        } else {
            await axios.post('/api/products', form);
        }

        setForm({ name: '', quantity: '', price: '' });
        fetchProducts();
    };

    const handleEdit = product => {
        setForm({
            name: product.name,
            quantity: product.quantity,
            price: product.price,
        });
        setEditing(product.id);
    };

    const totalSum = products.reduce((sum, p) => sum + p.total_value, 0);

    return (
        <div className="container mt-5">
            <h2 className="mb-4">Product Entry</h2>

            <form onSubmit={handleSubmit} className="mb-5">
                <div className="row g-3">
                    <div className="col-md-4">
                        <input type="text" name="name" value={form.name} onChange={handleChange} placeholder="Product name" className="form-control" required />
                    </div>
                    <div className="col-md-3">
                        <input type="number" name="quantity" value={form.quantity} onChange={handleChange} placeholder="Quantity in stock" className="form-control" required />
                    </div>
                    <div className="col-md-3">
                        <input type="number" step="0.01" name="price" value={form.price} onChange={handleChange} placeholder="Price per item" className="form-control" required />
                    </div>
                    <div className="col-md-2">
                        <button type="submit" className="btn btn-primary w-100">{editing ? 'Update' : 'Add'}</button>
                    </div>
                </div>
            </form>

            <table className="table table-striped">
                <thead>
                    <tr>
                        <th>Product name</th>
                        <th>Quantity in stock</th>
                        <th>Price per item</th>
                        <th>Datetime submitted</th>
                        <th>Total value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {products.map(product => (
                        <tr key={product.id}>
                            <td>{product.name}</td>
                            <td>{product.quantity}</td>
                            <td>${product.price}</td>
                            <td>{product.datetime}</td>
                            <td>${product.total_value.toFixed(2)}</td>
                            <td>
                                <button className="btn btn-sm btn-secondary" onClick={() => handleEdit(product)}>Edit</button>
                            </td>
                        </tr>
                    ))}
                    <tr className="fw-bold">
                        <td colSpan="4">Total</td>
                        <td>${totalSum.toFixed(2)}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    );
}
