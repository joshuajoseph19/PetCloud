import { StatusBar } from 'expo-status-bar';
import React, { useState, useEffect } from 'react';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Alert, Image, RefreshControl, Dimensions, ActivityIndicator } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

import { API_URL, getImageUrl, fetchWithTimeout } from './config';

export default function App() {
    const [screen, setScreen] = useState('login');
    const [user, setUser] = useState(null);

    // Dashboard Data
    const [dashboardData, setDashboardData] = useState(null);
    const [loading, setLoading] = useState(false);
    const [refreshing, setRefreshing] = useState(false);

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [isProcessing, setIsProcessing] = useState(false);
    const [menuVisible, setMenuVisible] = useState(false);
    const [activeItem, setActiveItem] = useState('Overview');

    // Adoption Screen State
    const [adoptionListings, setAdoptionListings] = useState([]);
    const [adoptionLoading, setAdoptionLoading] = useState(false);
    const [selectedCategory, setSelectedCategory] = useState('All Pets');

    const handleLogin = async () => {
        if (!email || !password) {
            Alert.alert('Missing Info', 'Please enter your email and password.');
            return;
        }

        setIsProcessing(true);
        console.log(`Connecting to: ${API_URL}/login.php`);

        try {
            const response = await fetchWithTimeout(`${API_URL}/login.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ email, password }),
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid response from server. Please check if PHP API is returning valid JSON.');
            }

            console.log('Login Result:', data);

            if (data.success) {
                setUser(data.user);
                setScreen('dashboard');
                fetchDashboardData(data.user.id);
            } else {
                Alert.alert('Login Failed', data.error || 'Invalid email or password.');
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            Alert.alert('Connection Error',
                error.message + '\n\n' +
                'Troubleshooting:\n' +
                '1. Ensure XAMPP (Apache & MySQL) is running.\n' +
                '2. Verify your Phone and PC are on the SAME Wi-Fi.\n' +
                '3. Check PC IP in config.js (Current: ' + API_URL + ')\n' +
                '4. Ensure Firewall is not blocking port 80.'
            );
        } finally {
            setIsProcessing(false);
        }
    };

    const fetchDashboardData = async (userId) => {
        setLoading(true);
        try {
            const response = await fetchWithTimeout(`${API_URL}/get_dashboard.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            });
            const data = await response.json();
            setDashboardData(data);
        } catch (error) {
            console.error('Dashboard Error:', error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    const onRefresh = () => {
        setRefreshing(true);
        if (user) fetchDashboardData(user.id);
    };

    const handleLogout = () => {
        setUser(null);
        setScreen('login');
        setEmail('');
        setPassword('');
        setDashboardData(null);
    };

    // Adoption Fetch Effect
    useEffect(() => {
        if (activeItem === 'Adoption') {
            const categories = [
                { id: null, name: 'All Pets' },
                { id: 1, name: 'Dogs' },
                { id: 2, name: 'Cats' },
                { id: 3, name: 'Rabbits' },
                { id: 4, name: 'Birds' }
            ];
            const cat = categories.find(c => c.name === selectedCategory);
            fetchAdoptionListings(cat?.id);
        }
    }, [activeItem, selectedCategory]);

    if (screen === 'login') {
        return (
            <View style={styles.container}>
                <View style={styles.authBox}>
                    <Image source={require('./assets/icon.png')} style={styles.logo} />
                    <Text style={styles.title}>Welcome Back!</Text>
                    <Text style={styles.subtitle}>Login to PetCloud</Text>

                    <TextInput
                        style={styles.input}
                        placeholder="Email Address"
                        value={email}
                        onChangeText={setEmail}
                        keyboardType="email-address"
                        autoCapitalize="none"
                    />
                    <TextInput
                        style={styles.input}
                        placeholder="Password"
                        value={password}
                        onChangeText={setPassword}
                        secureTextEntry
                    />

                    <TouchableOpacity
                        style={[styles.button, isProcessing && { opacity: 0.7 }]}
                        onPress={handleLogin}
                        disabled={isProcessing}
                    >
                        {isProcessing ? (
                            <ActivityIndicator color="white" />
                        ) : (
                            <Text style={styles.buttonText}>Sign In</Text>
                        )}
                    </TouchableOpacity>

                    <TouchableOpacity style={{ marginTop: 15 }}>
                        <Text style={{ color: '#3b82f6', textAlign: 'center' }}>Forgot Password?</Text>
                    </TouchableOpacity>
                </View>
                <StatusBar style="auto" />
            </View>
        );
    }

    // Loading State
    if (!dashboardData && loading) {
        return (
            <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color="#3b82f6" />
            </View>
        );
    }

    const {
        greeting, pets, feeding_schedules, appointments, reminders,
        nearbyLostPets, nearbyStrays, dailyTasks
    } = dashboardData || {};



    const categories = [
        { id: null, name: 'All Pets' },
        { id: 1, name: 'Dogs' },
        { id: 2, name: 'Cats' },
        { id: 3, name: 'Rabbits' },
        { id: 4, name: 'Birds' }
    ];

    const fetchAdoptionListings = async (typeId = null) => {
        setAdoptionLoading(true);
        try {
            let url = `${API_URL}/get_adoption_listings.php`;
            if (typeId) {
                url += `?pet_type_id=${typeId}`;
            }
            const response = await fetchWithTimeout(url);
            const data = await response.json();
            if (data.success) {
                setAdoptionListings(data.data || []);
            }
        } catch (error) {
            console.error("Adoption fetch error:", error);
        } finally {
            setAdoptionLoading(false);
        }
    };



    const renderAdoption = () => (
        <View style={styles.content}>
            <Text style={styles.pageTitle}>Find Your New Best Friend</Text>

            {/* Filter Tabs */}
            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.filterScroll}>
                {categories.map((cat) => (
                    <TouchableOpacity
                        key={cat.name}
                        style={[
                            styles.filterTab,
                            selectedCategory === cat.name && styles.filterTabActive
                        ]}
                        onPress={() => setSelectedCategory(cat.name)}
                    >
                        <Text style={[
                            styles.filterText,
                            selectedCategory === cat.name && styles.filterTextActive
                        ]}>
                            {cat.name}
                        </Text>
                    </TouchableOpacity>
                ))}
            </ScrollView>

            {/* Listings Grid */}
            {adoptionLoading ? (
                <ActivityIndicator size="large" color="#3b82f6" style={{ marginTop: 50 }} />
            ) : (
                <View style={styles.listingsGrid}>
                    {adoptionListings.map((pet) => (
                        <View key={pet.id} style={styles.adoptionCard}>
                            <Image
                                source={{ uri: getImageUrl(pet.image) }}
                                style={styles.adoptionImage}
                            />
                            <View style={styles.adoptionInfo}>
                                <View style={styles.adoptionHeader}>
                                    <Text style={styles.adoptionName}>{pet.pet_name}</Text>
                                    <View style={styles.typeTag}>
                                        <Text style={styles.typeTagText}>{pet.pet_type.name}</Text>
                                    </View>
                                </View>
                                <Text style={styles.adoptionDetails}>
                                    {pet.age.years} yrs • {pet.breed.name}
                                </Text>
                                <TouchableOpacity style={styles.viewProfileBtn}>
                                    <Text style={styles.viewProfileText}>View Profile</Text>
                                </TouchableOpacity>
                            </View>
                        </View>
                    ))}
                    {adoptionListings.length === 0 && (
                        <Text style={styles.emptyText}>No pets found in this category.</Text>
                    )}
                </View>
            )}
        </View>
    );



    const toggleMenu = () => {
        setMenuVisible(!menuVisible);
    };

    const renderSidebarItem = (name, icon) => {
        const isActive = activeItem === name;
        return (
            <TouchableOpacity
                style={[styles.sidebarItem, isActive && styles.sidebarItemActive]}
                onPress={() => {
                    setActiveItem(name);
                    setMenuVisible(false); // Optional: close menu on selection
                }}
            >
                <Ionicons
                    name={icon}
                    size={20}
                    color={isActive ? "white" : "#64748b"}
                />
                <Text style={[styles.sidebarText, isActive && styles.sidebarTextActive]}>
                    {name}
                </Text>
            </TouchableOpacity>
        );
    };

    const renderSidebar = () => (
        <React.Fragment>
            {menuVisible && (
                <TouchableOpacity
                    style={styles.modalOverlay}
                    activeOpacity={1}
                    onPress={() => setMenuVisible(false)}
                >
                    <View style={styles.sidebarContainer}>
                        <View style={styles.sidebarHeader}>
                            <Text style={styles.sidebarTitle}>PetCloud</Text>
                            <TouchableOpacity onPress={() => setMenuVisible(false)}>
                                <Ionicons name="close" size={24} color="#64748b" />
                            </TouchableOpacity>
                        </View>
                        <ScrollView style={styles.sidebarContent}>
                            {renderSidebarItem('Overview', 'grid-outline')}
                            {renderSidebarItem('Adoption', 'heart-outline')}
                            {renderSidebarItem('Pet Rehoming', 'paw-outline')}
                            {renderSidebarItem('My Pets', 'people-outline')}
                            {renderSidebarItem('Feeding Schedule', 'restaurant-outline')}
                            {renderSidebarItem('Smart Feeder', 'hardware-chip-outline')}
                            {renderSidebarItem('My Orders', 'cart-outline')}
                            {renderSidebarItem('Schedule', 'calendar-outline')}
                            {renderSidebarItem('Marketplace', 'storefront-outline')}
                            {renderSidebarItem('Health Records', 'fitness-outline')}
                            {renderSidebarItem('Lost Pet Reports', 'alert-circle-outline')}
                        </ScrollView>
                        <View style={styles.sidebarFooter}>
                            <TouchableOpacity style={styles.sidebarItem} onPress={handleLogout}>
                                <Ionicons name="log-out-outline" size={20} color="#ef4444" />
                                <Text style={[styles.sidebarText, { color: '#ef4444' }]}>Log Out</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </TouchableOpacity>
            )}
        </React.Fragment>
    );

    // Dashboard Screen with "Website Design"
    return (
        <View style={styles.dashboardContainer}>
            {renderSidebar()}

            {/* Header - mimics top-header */}
            <View style={styles.header}>
                <TouchableOpacity style={styles.menuBtn} onPress={toggleMenu}>
                    <Ionicons name="menu" size={28} color="#64748b" />
                </TouchableOpacity>

                <View style={styles.searchBar}>
                    <Ionicons name="search" size={18} color="#94a3b8" style={{ marginRight: 8 }} />
                    <Text style={{ color: '#94a3b8' }}>Search...</Text>
                </View>

                <TouchableOpacity style={styles.iconBtn} onPress={handleLogout}>
                    <Ionicons name="log-out-outline" size={24} color="#64748b" />
                </TouchableOpacity>
            </View>

            <ScrollView
                contentContainerStyle={styles.content}
                refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
            >
                {activeItem === 'Adoption' ? (
                    // Render Adoption Screen
                    <View style={{ marginTop: 20 }}>
                        <Text style={styles.pageTitle}>Find Your New Best Friend</Text>

                        {/* Filter Tabs */}
                        <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.filterScroll}>
                            {/* Assuming 'categories' is defined in scope, but we defined it inside renderAdoption earlier, wait. 
                                I put `categories` and `renderAdoption` inside the component body in the previous step, so it should be fine.
                                Actually, I defined `renderAdoption` as a function. It's better to call it directly.
                            */}
                            {categories.map((cat) => (
                                <TouchableOpacity
                                    key={cat.name}
                                    style={[
                                        styles.filterTab,
                                        selectedCategory === cat.name && styles.filterTabActive
                                    ]}
                                    onPress={() => setSelectedCategory(cat.name)}
                                >
                                    <Text style={[
                                        styles.filterText,
                                        selectedCategory === cat.name && styles.filterTextActive
                                    ]}>
                                        {cat.name}
                                    </Text>
                                </TouchableOpacity>
                            ))}
                        </ScrollView>

                        {/* Listings Grid */}
                        {adoptionLoading ? (
                            <ActivityIndicator size="large" color="#3b82f6" style={{ marginTop: 50 }} />
                        ) : (
                            <View style={styles.listingsGrid}>
                                {adoptionListings.map((pet) => (
                                    <View key={pet.id} style={styles.adoptionCard}>
                                        <Image
                                            source={{ uri: getImageUrl(pet.image) }}
                                            style={styles.adoptionImage}
                                        />
                                        <View style={styles.adoptionInfo}>
                                            <View style={styles.adoptionHeaderRow}>
                                                <Text style={styles.adoptionName}>{pet.pet_name}</Text>
                                                <View style={styles.typeTag}>
                                                    <Text style={styles.typeTagText}>{pet.pet_type.name}</Text>
                                                </View>
                                            </View>
                                            <Text style={styles.adoptionDetails}>
                                                {pet.age.years} • {pet.breed.name}
                                            </Text>
                                            <TouchableOpacity style={styles.viewProfileBtn}>
                                                <Text style={styles.viewProfileText}>View Profile</Text>
                                            </TouchableOpacity>
                                        </View>
                                    </View>
                                ))}
                                {adoptionListings.length === 0 && (
                                    <Text style={styles.emptyText}>No pets found in this category.</Text>
                                )}
                            </View>
                        )}
                    </View>
                ) : (
                    // Render Dashboard Overview
                    <>
                        {/* Lost Pet Alert Banner */}
                        {nearbyLostPets && nearbyLostPets.length > 0 && (
                            <View style={styles.lostBanner}>
                                <View style={styles.bannerIcon}>
                                    <Ionicons name="megaphone" size={24} color="white" />
                                </View>
                                <View style={{ flex: 1 }}>
                                    <Text style={styles.bannerTitle}>Lost Pet Alert Inside your City!</Text>
                                    <Text style={styles.bannerSubtitle}>{nearbyLostPets[0].pet_name} ({nearbyLostPets[0].pet_breed}) missing nearby.</Text>
                                </View>
                            </View>
                        )}

                        {/* Hero Section */}
                        <View style={styles.heroSection}>
                            <Image
                                source={require('./assets/dashboard_hero_v3.png')}
                                style={[styles.heroBackground, { borderRadius: 24 }]}
                            />

                            {/* Decorative Paw Print */}
                            <Ionicons
                                name="paw"
                                size={120}
                                color="rgba(15, 23, 42, 0.15)"
                                style={{ position: 'absolute', right: 20, top: 20 }}
                            />

                            {/* Glassmorphism Overlay */}
                            <View style={styles.heroOverlay}>
                                <Text style={styles.greetingText}>
                                    {greeting}, {user?.full_name?.split(' ')[0].toLowerCase()}!
                                </Text>
                                <Text style={styles.heroSubText}>
                                    {reminders && reminders.length > 0
                                        ? `Upcoming: ${reminders[0].title}`
                                        : "No active health alerts today. Keep up the great care!"}
                                </Text>
                            </View>
                        </View>

                        {/* My Family (Pets) */}
                        <View style={styles.sectionHeader}>
                            <Text style={styles.sectionTitle}>My Family</Text>
                            <TouchableOpacity><Text style={styles.linkText}>View All</Text></TouchableOpacity>
                        </View>

                        <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.petsScroll}>
                            {pets && pets.map((pet) => (
                                <View key={pet.id} style={styles.petCard}>
                                    <Image source={{ uri: getImageUrl(pet.pet_image) }} style={styles.petImage} />
                                    <Text style={styles.petName}>{pet.pet_name}</Text>
                                    <Text style={styles.petBreed}>{pet.pet_breed}</Text>
                                    {pet.status === 'Lost' && <Text style={styles.lostBadge}>LOST</Text>}
                                </View>
                            ))}
                            <TouchableOpacity style={styles.addPetCard}>
                                <Ionicons name="add" size={32} color="#94a3b8" />
                                <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: 'bold' }}>Add Pet</Text>
                            </TouchableOpacity>
                        </ScrollView>

                        {/* Grid Layout (Feeding & Appointments) */}
                        <View style={styles.gridContainer}>

                            {/* Feeding Schedule */}
                            <View style={styles.card}>
                                <View style={styles.cardHeader}>
                                    <View style={styles.iconTitle}>
                                        <View style={[styles.iconBox, { backgroundColor: '#fef3c7' }]}>
                                            <Ionicons name="restaurant" size={20} color="#d97706" />
                                        </View>
                                        <View>
                                            <Text style={styles.cardTitle}>Feeding Schedule</Text>
                                            <Text style={styles.cardSub}>Today</Text>
                                        </View>
                                    </View>
                                </View>

                                {feeding_schedules && feeding_schedules.length > 0 ? (
                                    feeding_schedules.map((item, index) => (
                                        <View key={index} style={styles.scheduleItem}>
                                            <View>
                                                <Text style={styles.schedMeal}>{item.meal_name}</Text>
                                                <Text style={styles.schedPet}>{item.pet_name}</Text>
                                            </View>
                                            <Text style={styles.schedTime}>{item.feeding_time.substring(0, 5)}</Text>
                                        </View>
                                    ))
                                ) : (
                                    <Text style={styles.emptyText}>No schedules set.</Text>
                                )}
                            </View>

                            {/* Upcoming Visits */}
                            <View style={styles.card}>
                                <View style={styles.cardHeader}>
                                    <View style={styles.iconTitle}>
                                        <View style={[styles.iconBox, { backgroundColor: '#f3e8ff' }]}>
                                            <Ionicons name="medkit" size={20} color="#9333ea" />
                                        </View>
                                        <Text style={styles.cardTitle}>Upcoming Visits</Text>
                                    </View>
                                </View>

                                {appointments && appointments.length > 0 ? (
                                    appointments.map((appt, i) => (
                                        <View key={i} style={styles.apptItem}>
                                            <View style={styles.dateBox}>
                                                <Text style={styles.dateNum}>{appt.appointment_date.split('-')[2]}</Text>
                                            </View>
                                            <View style={{ flex: 1 }}>
                                                <Text style={styles.apptTitle}>{appt.service_type}</Text>
                                                <Text style={styles.apptPet}>{appt.pet_name} • {appt.hospital_name || 'Clinic'}</Text>
                                            </View>
                                        </View>
                                    ))
                                ) : (
                                    <Text style={styles.emptyText}>No upcoming appointments.</Text>
                                )}
                            </View>

                            {/* Nearby Lost Pets */}
                            {nearbyLostPets && nearbyLostPets.length > 0 && (
                                <View style={[styles.card, { borderLeftWidth: 4, borderLeftColor: '#ef4444' }]}>
                                    <View style={styles.cardHeader}>
                                        <View style={styles.iconTitle}>
                                            <View style={[styles.iconBox, { backgroundColor: '#fee2e2' }]}>
                                                <Ionicons name="search" size={20} color="#ef4444" />
                                            </View>
                                            <Text style={styles.cardTitle}>Lost Pets Near You</Text>
                                        </View>
                                    </View>

                                    {nearbyLostPets.map((lost, i) => (
                                        <View key={i} style={styles.lostItem}>
                                            <Image source={{ uri: getImageUrl(lost.pet_image) }} style={styles.lostItemImg} />
                                            <View style={{ flex: 1 }}>
                                                <Text style={styles.lostItemName}>{lost.pet_name} ({lost.pet_breed})</Text>
                                                <Text style={styles.lostItemLoc}>{lost.last_seen_location}</Text>
                                            </View>
                                            <TouchableOpacity style={styles.sightingBtn}>
                                                <Text style={styles.sightingBtnText}>Report</Text>
                                            </TouchableOpacity>
                                        </View>
                                    ))}
                                </View>
                            )}

                        </View>
                    </>
                )}

                <View style={{ height: 40 }} />
            </ScrollView>
            <StatusBar style="dark" />
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#3b82f6',
        alignItems: 'center',
        justifyContent: 'center',
        padding: 20,
    },
    loadingContainer: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#f8fafc',
    },
    authBox: {
        width: '100%',
        backgroundColor: 'white',
        padding: 30,
        borderRadius: 20,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.1,
        shadowRadius: 10,
        elevation: 8,
        alignItems: 'center',
    },
    logo: {
        width: 60,
        height: 60,
        borderRadius: 30,
        marginBottom: 15,
    },
    title: {
        fontSize: 24,
        fontWeight: 'bold',
        marginBottom: 5,
        color: '#1e293b',
    },
    subtitle: {
        fontSize: 14,
        color: '#64748b',
        marginBottom: 25,
    },
    input: {
        width: '100%',
        height: 50,
        backgroundColor: '#f1f5f9',
        borderRadius: 12,
        paddingHorizontal: 15,
        marginBottom: 15,
        fontSize: 16,
    },
    button: {
        backgroundColor: '#3b82f6',
        width: '100%',
        height: 50,
        borderRadius: 12,
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: 10,
        shadowColor: '#3b82f6',
        shadowOpacity: 0.4,
        shadowOffset: { width: 0, height: 4 },
        elevation: 4,
    },
    buttonText: {
        color: 'white',
        fontWeight: 'bold',
        fontSize: 16,
    },

    // Dashboard Styles
    dashboardContainer: {
        flex: 1,
        backgroundColor: '#f8fafc',
        paddingTop: 50,
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: 20,
        marginBottom: 20,
        gap: 15,
    },
    menuBtn: {
        padding: 5,
    },
    iconBtn: {
        padding: 5,
        backgroundColor: '#f1f5f9',
        borderRadius: 20,
    },
    searchBar: {
        flex: 1,
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: 'white',
        height: 45,
        borderRadius: 25,
        paddingHorizontal: 15,
        borderWidth: 1,
        borderColor: '#e2e8f0',
    },
    content: {
        paddingHorizontal: 20,
        paddingBottom: 40,
    },
    heroSection: {
        height: 200,
        borderRadius: 24,
        overflow: 'hidden',
        marginBottom: 25,
        position: 'relative',
        backgroundColor: '#dbeafe',
    },
    heroBackground: {
        width: '100%',
        height: '100%',
        position: 'absolute',
    },
    heroOverlay: {
        position: 'absolute',
        bottom: 0,
        width: '100%',
        backgroundColor: 'rgba(255,255,255,0.6)',
        padding: 20,
        borderTopWidth: 1,
        borderColor: 'rgba(255,255,255,0.4)',
        backdropFilter: 'blur(10px)', // iOS only
    },
    greetingText: {
        fontSize: 28,
        fontWeight: '800',
        color: '#0f172a',
        marginBottom: 5,
    },
    heroSubText: {
        fontSize: 14,
        color: '#334155',
        lineHeight: 20,
    },
    sectionHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 15,
    },
    sectionTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#1e293b',
    },
    linkText: {
        color: '#3b82f6',
        fontWeight: '600',
    },
    petsScroll: {
        marginBottom: 25,
    },
    petCard: {
        backgroundColor: 'white',
        padding: 15,
        borderRadius: 20,
        alignItems: 'center',
        marginRight: 15,
        borderWidth: 1,
        borderColor: '#f1f5f9',
        width: 110,
        shadowColor: '#000',
        shadowOpacity: 0.05,
        shadowOffset: { width: 0, height: 2 },
        elevation: 2,
    },
    addPetCard: {
        backgroundColor: '#f8fafc',
        padding: 15,
        borderRadius: 20,
        alignItems: 'center',
        justifyContent: 'center',
        marginRight: 15,
        borderWidth: 2,
        borderColor: '#e2e8f0',
        borderStyle: 'dashed',
        width: 110,
        height: 140,
    },
    petImage: {
        width: 60,
        height: 60,
        borderRadius: 30,
        marginBottom: 10,
        backgroundColor: '#e2e8f0',
    },
    petName: {
        fontSize: 14,
        fontWeight: 'bold',
        color: '#1e293b',
        marginBottom: 2,
        textAlign: 'center',
    },
    petBreed: {
        fontSize: 11,
        color: '#94a3b8',
        textAlign: 'center',
    },
    lostBadge: {
        position: 'absolute',
        top: 10,
        right: 10,
        backgroundColor: '#fee2e2',
        color: '#ef4444',
        fontSize: 8,
        fontWeight: 'bold',
        paddingHorizontal: 6,
        paddingVertical: 2,
        borderRadius: 8,
    },
    gridContainer: {
        gap: 20,
    },
    card: {
        backgroundColor: 'white',
        borderRadius: 20,
        padding: 20,
        shadowColor: '#000',
        shadowOpacity: 0.05,
        shadowOffset: { width: 0, height: 2 },
        elevation: 2,
    },
    cardHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: 15,
    },
    iconTitle: {
        flexDirection: 'row',
        gap: 12,
        alignItems: 'center',
    },
    iconBox: {
        width: 36,
        height: 36,
        borderRadius: 10,
        alignItems: 'center',
        justifyContent: 'center',
    },
    cardTitle: {
        fontSize: 16,
        fontWeight: 'bold',
        color: '#1e293b',
    },
    cardSub: {
        fontSize: 12,
        color: '#64748b',
    },
    scheduleItem: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingVertical: 12,
        borderBottomWidth: 1,
        borderBottomColor: '#f1f5f9',
    },
    schedMeal: {
        fontWeight: '600',
        color: '#334155',
    },
    schedPet: {
        fontSize: 12,
        color: '#94a3b8',
    },
    schedTime: {
        color: '#10b981',
        fontWeight: 'bold',
    },
    emptyText: {
        textAlign: 'center',
        color: '#94a3b8',
        padding: 10,
        fontStyle: 'italic',
    },
    apptItem: {
        flexDirection: 'row',
        gap: 12,
        marginBottom: 12,
        alignItems: 'center',
        backgroundColor: '#f8fafc',
        padding: 10,
        borderRadius: 12,
    },
    dateBox: {
        backgroundColor: 'white',
        paddingHorizontal: 10,
        paddingVertical: 5,
        borderRadius: 8,
        alignItems: 'center',
        minWidth: 45,
    },
    dateNum: {
        fontWeight: 'bold',
        color: '#334155',
    },
    apptTitle: {
        fontWeight: '600',
        fontSize: 14,
        color: '#1e293b',
    },
    apptPet: {
        fontSize: 12,
        color: '#64748b',
    },
    lostBanner: {
        backgroundColor: '#fff1f2',
        borderWidth: 1,
        borderColor: '#fecaca',
        borderRadius: 20,
        padding: 15,
        flexDirection: 'row',
        alignItems: 'center',
        gap: 15,
        marginBottom: 20,
    },
    bannerIcon: {
        backgroundColor: '#ef4444',
        width: 45,
        height: 45,
        borderRadius: 25,
        alignItems: 'center',
        justifyContent: 'center',
    },
    bannerTitle: {
        color: '#991b1b',
        fontWeight: 'bold',
        fontSize: 14,
    },
    bannerSubtitle: {
        color: '#b91c1c',
        fontSize: 12,
    },
    lostItem: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: 12,
        paddingVertical: 10,
        borderBottomWidth: 1,
        borderBottomColor: '#fee2e2',
    },
    lostItemImg: {
        width: 45,
        height: 45,
        borderRadius: 10,
    },
    lostItemName: {
        fontWeight: 'bold',
        color: '#991b1b',
        fontSize: 13,
    },
    lostItemLoc: {
        fontSize: 11,
        color: '#b91c1c',
    },
    sightingBtn: {
        backgroundColor: '#ef4444',
        paddingHorizontal: 12,
        paddingVertical: 6,
        borderRadius: 8,
    },
    sightingBtnText: {
        color: 'white',
        fontSize: 11,
        fontWeight: 'bold',
    },
    // Sidebar Styles
    modalOverlay: {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: 'rgba(0,0,0,0.5)',
        zIndex: 1000,
        flexDirection: 'row',
    },
    sidebarContainer: {
        width: '80%',
        backgroundColor: 'white',
        height: '100%',
        paddingTop: 50,
        paddingBottom: 20,
        shadowColor: "#000",
        shadowOffset: {
            width: 0,
            height: 2,
        },
        shadowOpacity: 0.25,
        shadowRadius: 3.84,
        elevation: 5,
    },
    sidebarHeader: {
        paddingHorizontal: 20,
        paddingBottom: 20,
        borderBottomWidth: 1,
        borderBottomColor: '#f1f5f9',
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
    },
    sidebarTitle: {
        fontSize: 22,
        fontWeight: 'bold',
        color: '#1e293b',
    },
    sidebarContent: {
        flex: 1,
        paddingVertical: 10,
    },
    sidebarItem: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: 15,
        paddingHorizontal: 20,
        gap: 15,
        marginHorizontal: 10,
    },
    sidebarItemActive: {
        backgroundColor: '#3b82f6',
        borderRadius: 12,
    },
    sidebarText: {
        fontSize: 16,
        color: '#475569',
        fontWeight: '500',
    },
    sidebarTextActive: {
        color: 'white',
        fontWeight: 'bold',
    },
    sidebarFooter: {
        paddingVertical: 10,
        borderTopWidth: 1,
        borderTopColor: '#f1f5f9',
    },

    // Adoption Screen Styles
    pageTitle: {
        fontSize: 24,
        fontWeight: '800', // Outfit-Bold eq
        color: '#0f172a',
        marginBottom: 20,
    },
    filterScroll: {
        marginBottom: 25,
        maxHeight: 50,
    },
    filterTab: {
        paddingHorizontal: 20,
        paddingVertical: 10,
        backgroundColor: 'white',
        borderRadius: 25,
        marginRight: 10,
        borderWidth: 1,
        borderColor: '#e2e8f0',
        height: 40,
        justifyContent: 'center',
    },
    filterTabActive: {
        backgroundColor: '#0f172a', // Dark slate/black for active filter
        borderColor: '#0f172a',
    },
    filterText: {
        fontSize: 14,
        fontWeight: '600',
        color: '#64748b',
    },
    filterTextActive: {
        color: 'white',
    },
    listingsGrid: {
        gap: 20,
    },
    adoptionCard: {
        backgroundColor: 'white',
        borderRadius: 20,
        overflow: 'hidden',
        borderWidth: 1,
        borderColor: '#f1f5f9',
        shadowColor: '#000',
        shadowOpacity: 0.05,
        shadowOffset: { width: 0, height: 4 },
        shadowRadius: 10,
        elevation: 3,
    },
    adoptionImage: {
        width: '100%',
        height: 250,
        backgroundColor: '#e2e8f0',
    },
    adoptionInfo: {
        padding: 20,
    },
    adoptionHeaderRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
        marginBottom: 5,
    },
    adoptionName: {
        fontSize: 22,
        fontWeight: 'bold',
        color: '#1e293b',
    },
    typeTag: {
        backgroundColor: '#eff6ff',
        paddingHorizontal: 10,
        paddingVertical: 4,
        borderRadius: 8,
    },
    typeTagText: {
        fontSize: 12,
        fontWeight: '700',
        color: '#3b82f6',
    },
    adoptionDetails: {
        fontSize: 14,
        color: '#64748b',
        marginBottom: 20,
    },
    viewProfileBtn: {
        backgroundColor: '#10b981', // Green button
        paddingVertical: 12,
        borderRadius: 12,
        alignItems: 'center',
    },
    viewProfileText: {
        color: 'white',
        fontSize: 15,
        fontWeight: '700',
    },
});
